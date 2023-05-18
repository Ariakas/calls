<template>
    <div id="home">
        <Text_input label="Ссылка на эту комнату" :readonly="true" v-model="link"/>
        <div class="controls">
            <Text_button :disabled="connected" text="Подключиться" @click="connect"/>
            <Text_button :disabled="!connected" text="Отключиться" @click="disconnect"/>
        </div>
        <div class="videos_container">
            <div class="video remote" ref="remote">
                <p></p>
            </div>
            <div class="video local" ref="local">
                <p></p>
            </div>
        </div>
    </div>
</template>

<script>
import Text_input from "@/components/Text_input.vue";
import Text_button from "@/components/Text_button.vue";
import AgoraRTC from "agora-rtc-sdk-ng";
import {mapGetters} from "vuex";
import request from "@/functions/Fetch";

export default {
    name: 'HomeView',
    components: {
        Text_input,
        Text_button
    },
    data() {
        return {
            hash: "",
            link: "",
            options: {
                appId: "88cd8f017a414ae59f6070ecfab0ebb7",
                channel: "",
                token: "",
                uid: null
            },
            channelParameters: {
                localAudioTrack: null,
                localVideoTrack: null,
                remoteAudioTrack: null,
                remoteVideoTrack: null,
                remoteUid: null,
            },
            agoraEngine: null,
            connected: false,
            indicate_interval: null,
            is_host: false
        }
    },
    computed: {
        ...mapGetters([
            "get_user_id"
        ])
    },
    methods: {
        async connect() {
            this.connected = true;
            await this.agoraEngine.join(this.options.appId, this.options.channel, this.options.token, this.get_user_id);
            this.channelParameters.localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
            this.channelParameters.localVideoTrack = await AgoraRTC.createCameraVideoTrack();
            await this.agoraEngine.publish([this.channelParameters.localAudioTrack, this.channelParameters.localVideoTrack]);
            this.channelParameters.localVideoTrack.play(this.$refs.local.querySelector("div"));
            if (!this.is_host) {
                request("/connect", {
                    link: this.hash
                });
            }
        },
        async disconnect() {
            this.connected = false;
            this.channelParameters.localAudioTrack.close();
            this.channelParameters.localVideoTrack.close();
            await this.agoraEngine.leave();
        }
    },
    mounted() {
        let hash = location.hash.replace("#", "");
        if (hash) {
            request("/test_link", {
                link: hash
            }).then(response => {
                if (response.status === "success") {
                    this.hash = hash;
                    this.options.channel = hash;
                    request("/get_rtc_token", {
                        link: this.hash
                    }).then(response => {
                        this.options.token = response.detail;
                        this.link = location.href;
                        this.agoraEngine = AgoraRTC.createClient({mode: "rtc", codec: "vp8"});
                        this.$refs.remote.append(document.createElement("div"));
                        this.$refs.local.append(document.createElement('div'));

                        this.agoraEngine.on("user-published", async (user, mediaType) => {
                            await this.agoraEngine.subscribe(user, mediaType);
                            if (mediaType === "video") {
                                this.channelParameters.remoteVideoTrack = user.videoTrack;
                                this.channelParameters.remoteAudioTrack = user.audioTrack;
                                this.channelParameters.remoteVideoTrack.play(this.$refs.remote.querySelector("div"));
                            }
                            if (mediaType === "audio") {
                                this.channelParameters.remoteAudioTrack = user.audioTrack;
                                this.channelParameters.remoteAudioTrack.play();
                            }
                        });
                    })
                }
            })
        }
        else {
            this.is_host = true;
            request("/get_link").then(response => {
                this.hash = response.detail;
                this.options.channel = response.detail;
                request("/get_rtc_token", {
                    link: this.hash
                }).then(response => {
                    this.options.token = response.detail;
                    this.link = `${location.href}#${this.hash}`;
                    this.agoraEngine = AgoraRTC.createClient({mode: "rtc", codec: "vp8"});
                    this.$refs.remote.append(document.createElement("div"));
                    this.$refs.local.append(document.createElement('div'));

                    this.agoraEngine.on("user-published", async (user, mediaType) => {
                        await this.agoraEngine.subscribe(user, mediaType);
                        if (mediaType === "video") {
                            this.channelParameters.remoteVideoTrack = user.videoTrack;
                            this.channelParameters.remoteAudioTrack = user.audioTrack;
                            this.channelParameters.remoteVideoTrack.play(this.$refs.remote.querySelector("div"));
                        }
                        if (mediaType === "audio") {
                            this.channelParameters.remoteAudioTrack = user.audioTrack;
                            this.channelParameters.remoteAudioTrack.play();
                        }
                        this.indicate_interval = setInterval(() => {
                            request("/indicate_usage", {
                                link: this.hash
                            });
                        }, 60 * 1000);
                    });
                    this.agoraEngine.on("user-unpublished", () => {
                        clearInterval(this.indicate_interval);
                    });
                })
            });
        }
    }
}
</script>

<style lang="less" scoped>
@import "@/assets/common";

#home {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    .controls {
        display: flex;
        gap: 1rem;
    }
    .videos_container {
        flex: 1;
        margin-top: 1rem;
        display: grid;
        grid-template-columns: 3fr 1fr;
        grid-template-rows: calc(100vh - 15rem);
        grid-gap: 1rem;
        .video {
            width: 100%;
            height: 100%;
            position: relative;
            p {
                position: absolute;
                font-weight: bold;
                color: #fff;
                margin: 0;
                top: 1rem;
                font-size: 1.5rem;
                text-shadow: 0 0 1px #000, 0 0 1px #000, 0 0 1px #000, 0 0 1px #000;
                z-index: 2;
                text-align: center;
                width: 100%;
            }
            > :deep(div) {
                width: 100%;
                height: 100%;
                > div {
                    background: none !important;
                    width: 100%;
                    height: 100%;
                    video, img {
                        position: static !important;
                        display: block;
                        max-width: 100%;
                        max-height: 100%;
                        width: 100%;
                        height: 100%;
                        margin: auto;
                        object-fit: contain !important;
                        object-position: top;
                        filter: drop-shadow(0 0 3px @accent_color);
                    }
                }
            }
        }
    }
}
</style>
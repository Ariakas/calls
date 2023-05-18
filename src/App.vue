<template>
    <nav v-if="get_user_id">
        <router-link to="/">Главная</router-link>
        |
        <router-link to="/history">История звонков</router-link>
        |
        <a href="#" @click="logout">Выход</a>
    </nav>
    <router-view/>
    <div id="popup_message" v-if="get_popup_message" @click="close_popup">
        <div class="message">
            <p>{{ get_popup_message }}</p>
        </div>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import request from "@/functions/Fetch";

export default {
    computed: {
        ...mapGetters([
            "get_user_id",
            "get_popup_message"
        ])
    },
    methods: {
        close_popup() {
            this.$store.commit("unset_popup_message");
        },
        logout() {
            request("/logout").then(response => {
                if (response.status === "success") {
                    this.$store.commit("unset_user_id");
                    this.$router.push("/auth");
                }
            });
        }
    }
}
</script>

<style lang="less">
html, body {
    margin: 0;
    min-height: 100vh;
}

@import "assets/common.less";

#app {
    font-family: Arial, sans-serif;
    color: #2c3e50;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
nav {
    display: flex;
    padding: 1rem;
    gap: 1rem;
}
nav a {
    font-weight: bold;
    color: #2c3e50;
    text-decoration: none;
}
nav a.router-link-exact-active {
    color: @accent_color;
}

#popup_message {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: fade(#fff, 90%);
    display: flex;
    .message {
        width: 300px;
        padding: 1rem;
        margin: auto;
        background: #fff;
        box-shadow: 0 3px 6px fade(#000, 15%);
        p {
            text-align: center;
        }
    }
}
</style>

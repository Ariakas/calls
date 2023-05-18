<template>
    <div id="auth">
        <div class="form" v-if="login_form_active">
            <Text_input label="Логин" v-model="login" />
            <Text_input label="Пароль" v-model="password" />
            <Text_button text="Вход" @click="do_login" />
            <p @click="switch_to_register">У меня нет учётной записи</p>
        </div>
        <div class="form" v-if="!login_form_active">
            <Text_input label="Логин" v-model="login" />
            <Text_input label="Имя" v-model="name" />
            <Text_input label="Пароль" v-model="password" />
            <Text_button text="Регистрация" @click="do_register" />
            <p @click="switch_to_login">У меня есть учётная запись</p>
        </div>
    </div>
</template>

<script>
import Text_input from "@/components/Text_input.vue";
import Text_button from "@/components/Text_button.vue";
import request from "@/functions/Fetch";
export default {
    name: "Auth_view",
    data() {
        return {
            login: "",
            password: "",
            name: "",
            login_form_active: true
        }
    },
    components: {
        Text_input,
        Text_button
    },
    methods: {
        switch_to_register() {
            this.login_form_active = false;
            this.login = "";
            this.password = "";
        },
        switch_to_login() {
            this.login = "";
            this.password = "";
            this.name = "";
            this.login_form_active = true;
        },
        do_login() {
            request("/login", {
                login: this.login,
                password: this.password
            }).then(response => {
                if (response.status === "success") {
                    this.$store.commit("set_user_id", response.detail);
                    this.$router.push("/");
                }
                else {
                    this.$store.commit("set_popup_message", response.detail);
                }
            });
        },
        do_register() {
            request("/register", {
                login: this.login,
                password: this.password,
                name: this.name
            }).then(response => {
                if (response.status === "success") {
                    this.$store.commit("set_popup_message", "Вы успешно зарегистрировались");
                    this.switch_to_login();
                }
                else {
                    this.$store.commit("set_popup_message", response.detail);
                }
            });
        }
    },
    mounted() {
        request("/get_user_id").then(response => {
            if (response.status === "success") {
                this.$store.commit("set_user_id", response.detail);
                this.$router.push("/");
            }
        })
    }
}
</script>

<style scoped lang="less">
@import "@/assets/common";

#auth {
    display: flex;
    min-height: 100vh;
    width: 100%;
    .form {
        margin: auto;
        width: 300px;
        p {
            text-align: center;
            color: darken(@accent_color, 10%);
            cursor: pointer;
        }
    }
}
</style>
import {createRouter, createWebHistory} from "vue-router";
import store from "@/store";
import request from "@/functions/Fetch";

const routes = [
    {
        path: "/",
        name: "home",
        meta: {
            title: "Главная"
        },
        component: () => import("@/views/Home_view.vue")
    },
    {
        path: "/auth",
        name: "auth",
        meta: {
            title: "Вход"
        },
        component: () => import("@/views/Auth_view.vue")
    },
    {
        path: "/history",
        name: "history",
        meta: {
            title: "История звонков"
        },
        component: () => import("@/views/History_view.vue")
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to) => {
    document.title = to.meta.title;
    let authed = store.getters.get_user_id;
    if (!authed) {
        await request("/get_user_id").then(response => {
            if (response.status === "success") {
                store.commit("set_user_id", response.detail);
                authed = response.detail;
            }
        });
    }
    if (!authed && to.path !== "/auth") {
        return "/auth";
    }
    if (authed && to.path === "/auth") {
        return "/";
    }
})

export default router;

<template>
    <div id="history">
        <h2>История звонков</h2>
        <div class="call" v-for="(call, index) in history" :key="index">
            <p>Дата: {{ call.date }}</p>
            <p>Участник: {{ call.name }}</p>
            <p>Продолжительность: {{ call.duration }}</p>
        </div>
    </div>
</template>

<script>
import request from "@/functions/Fetch";

export default {
    data() {
        return {
            history: []
        }
    },
    mounted() {
        request("/get_history").then(response => {
            this.history = response.detail
        });
    }
}
</script>

<style lang="less" scoped>
@import "@/assets/common";

#history {
    padding: 1rem;
    .call {
        padding: 1rem 2rem;
        &:nth-child(even) {
            background: fade(@accent_color, 10%);
        }
    }
}
</style>
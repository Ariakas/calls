<template>
    <button :disabled="disabled">{{ text }}</button>
</template>

<script>
export default {
    name: "Text_button",
    props: {
        text: {
            required: true
        },
        disabled: {
            default: false,
            type: Boolean
        }
    }
}
</script>

<style lang="less" scoped>
@import "@/assets/common";

button {
    padding: 1rem;
    background: transparent;
    font-family: sans-serif;
    font-size: 1rem;
    width: 100%;
    cursor: pointer;
    transition: all .35s;
    background: @accent_color;
    color: #fff;
    border-color: transparent;
    font-weight: bold;
    &:hover {
        background: darken(@accent_color, 10%);
    }
    &[disabled] {
        filter: grayscale(100%);
        cursor: not-allowed;
    }
}
</style>
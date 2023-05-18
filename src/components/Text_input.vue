<template>
    <div class="input">
        <label>
            <span v-if="label" :class="{ focused: focus || modelValue }">{{ label }}</span>
            <input :readonly="readonly" @focus="focused" @blur="blurred" :type="type" @input="$emit('update:modelValue', $event.target.value)" :value="modelValue">
        </label>
    </div>
</template>

<script>
export default {
    name: "Text_input",
    data() {
        return {
            focus: false
        }
    },
    props: {
        label: String,
        type: {
            default: "text",
            type: String
        },
        readonly: {
            type: Boolean,
            default: false
        },
        modelValue: String
    },
    methods: {
        focused() {
            this.focus = true;
        },
        blurred() {
            this.focus = false;
        }
    }
}
</script>

<style lang="less" scoped>
@import "@/assets/common";

.input:not(:last-child) {
    margin: 1.5rem 0;
}
label, span, input {
    display: block;
    font-family: sans-serif;
    width: 100%;
    font-size: 1rem;
    color: #1d1d1d;
}
label {
    position: relative;
}
span {
    position: absolute;
    left: 0.85rem;
    height: 1rem;
    top: .85rem;
    width: auto;
    background: #fff;
    padding: 0 .25rem;
    transition: all .15s;
    cursor: text;
    &.focused {
        top: -.5rem;
        color: fade(#000, 50%);
        font-size: .85rem;
    }
}
input {
    padding: .75rem 1rem;
    outline: none;
    border: 2px solid fade(#1d1d1d, 50%);
    box-sizing: border-box;
    &:focus {
        border-color: @accent_color;
    }
}
</style>
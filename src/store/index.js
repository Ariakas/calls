import {createStore} from 'vuex'

export default createStore({
    state: {
        user_id: null,
        popup_message: ""
    },
    getters: {
        get_user_id(state) {
            return state.user_id;
        },
        get_popup_message(state) {
            return state.popup_message;
        }
    },
    mutations: {
        set_user_id(state, id) {
            state.user_id = id;
        },
        unset_user_id(state) {
            state.user_id = null;
        },
        set_popup_message(state, message) {
            state.popup_message = message;
        },
        unset_popup_message(state) {
            state.popup_message = "";
        },
    },
    actions: {},
    modules: {}
})

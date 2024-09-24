import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        counter: 1000,
        deleteModalobj: {
            showDeleteModal: false,
            deleteUrl: "",
            data: null,
            deletingIndex: -1,
            isDeleted: false
        },
        user : false
    },
    getters: {
        getCounter(state) {
            return state.counter;
        },
        getDeleteModalobj(state) {
            return state.deleteModalobj;
        },
        // loggedInuser(state){
        //     return 
        // }
    },
    mutations: {
        changeTheCounter(state, data) {
            state.counter += data;
        },
        setDeleteModal(state, data) {
            state.deleteModalobj = {
                ...state.deleteModalobj, // Keep the existing state properties
                showDeleteModal: false, // Close the modal
                deleteUrl: "",
                data: null,
                deletingIndex: data.deletingIndex,
                isDeleted: data.isDeleted // Update isDeleted state
            };
        },
        setDeletingModalObj(state, data) {
            state.deleteModalobj = data;
        },
        updateUser(state, data) {
            state.user=data
        }
    },
    actions: {
        changeCounterAction({ commit }, data) {
            commit("changeTheCounter", data);
        }
    }
});

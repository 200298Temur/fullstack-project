<template>
    <div>
        <!-- Delete Confirmation Modal -->
        <Modal
            :value="getDeleteModalobj.showDeleteModal"
            :mask-closable="false"
            :closable="false"
            width="360"
        >
            <p slot="header" style="color:#f60;text-align:center">
                <Icon type="ios-information-circle"></Icon>
                <span>Delete confirmation</span>
            </p>
            <div style="text-align:center">
                <p>Are you sure you want to delete this tag?</p>
            </div>
            <div slot="footer">
                <Button type="default" size="large" @click="closeModal"
                    >Close</Button
                >
                <Button
                    type="error"
                    size="large"
                    long
                    :loading="isDeleting"
                    :disabled="isDeleting"
                    @click="deleteTag"
                    >Delete</Button
                >
            </div>
        </Modal>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data() {
        return {
            isDeleting: false // Controls loading state
        };
    },
    methods: {
        async deleteTag() {
            this.isDeleting = true; // Start loading

            try {
                const res = await this.callApi(
                    "delete",
                    this.getDeleteModalobj.deleteUrl,
                    this.getDeleteModalobj.data
                );
                this.s("Tag has been deleted successfully!"); // Success message
                this.$store.commit("setDeleteModal", { isDeleted: true }); // Reset modal state
            } catch (e) {
                this.swr(); // Error handling
                this.$store.commit("setDeleteModal", { isDeleted: false }); // Reset modal state on failure
            }
            this.isDeleting = false; // Stop loading
        },
        closeModal() {
            this.$store.commit("setDeleteModal", { isDeleted: false }); // Close the modal manually
        }
    },
    computed: {
        ...mapGetters(["getDeleteModalobj"]) // Access modal object from Vuex
    }
};
</script>

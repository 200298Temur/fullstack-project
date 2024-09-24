<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
                <div
                    class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
                >
                    <p class="_title0">
                        Role Managment
                        <Button @click="addModal = true"><Icon type="md-add" /> Add a new role</Button>
                    </p>

                    <div class="_overflow _table_div">
                        <table class="_table">
                            <!-- TABLE TITLE -->
                            <tr>
                                <th>ID</th>
                                <th>Role Type</th>
                                <th>Created at</th>
                                <th>Action</th>
                            </tr>
                            <!-- TABLE TITLE -->

                            <!-- ITEMS -->
                            <tr
                                v-for="(tag, i) in tags"
                                :key="i"
                                v-if="tags.length"
                            >
                                <td>{{ tag.id }}</td>
                                <td class="_table_name">{{ tag.roleName }}</td>
                                <!-- <td class="">{{ tag.permission }}</td> -->

                                <td>{{ tag.created_at }}</td>
                                <td>
                                    <Button
                                        size="small"
                                        type="info"
                                        @click="showEditModal(tag, i)"
                                        >Edit</Button
                                    >
                                    <Button
                                        type="error"
                                        size="small"
                                        @click="showDeletingModal(tag, i)"
                                        :loading="tag.isDeleting"
                                        >Delete</Button
                                    >
                                </td>
                            </tr>
                            <!-- ITEMS -->
                        </table>
                    </div>
                </div>

                <!-- Add Modal -->
                <Modal
                    v-model="addModal"
                    title="Add role"
                    :mask-closable="false"
                    :closable="false"
                >
                    <Input
                        v-model="data.roleName"
                        placeholder="Role Name"
                        style="width: 300px"
                    />
                    <div slot="footer">
                        <Button type="default" @click="addModal = false"
                            >Close</Button
                        >
                        <Button
                            type="primary"
                            @click="add"
                            :disabled="isAdding"
                            :loading="isAdding"
                        >
                            {{ isAdding ? "Adding.." : "Add Role" }}
                        </Button>
                    </div>
                </Modal>

                <!-- Edit Modal -->
                <Modal
                    v-model="editModal"
                    title="Edit role"
                    :mask-closable="false"
                    :closable="false"
                >
                    <Input
                        v-model="editData.roleName"
                        placeholder="Edit role name"
                        style="width: 300px"
                    />
                    <div slot="footer">
                        <Button type="default" @click="editModal = false"
                            >Close</Button
                        >
                        <Button
                            type="primary"
                            @click="edit"
                            :disabled="isEditing"
                            :loading="isEditing"
                        >
                            {{ isEditing ? "Editing.." : "Edit role" }}
                        </Button>
                    </div>
                </Modal>

                <!-- Delete Confirmation Modal -->
                <deleteModal></deleteModal>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import deleteModal from "../components/deleteModal.vue";

export default {
    data() {
        return {
            data: { roleName: "" },
            addModal: false,
            editModal: false,
            isAdding: false,
            isEditing: false,
            isDeleting: false, // Added this property
            tags: [],
            editData: { id: null, roleName: "" },
            index: -1
        };
    },
    methods: {
        async add() {
            if (this.data.roleName.trim() === "") {
                return this.e("Role name is required");
            }

            this.isAdding = true;
            try {
                const res = await this.callApi(
                    "post",
                    "/app/create_role",
                    this.data
                );
                this.tags.unshift(res.data);
                this.s("Role has been added successfully");
                this.addModal = false;
                this.data.roleName = "";
            } catch (e) {
                if (e.response && e.response.status === 422) {
                    this.i(e.response.data.errors.roleName[0]);
                } else {
                    this.swr();
                }
            } finally {
                this.isAdding = false;
            }
        },
        async edit() {
            if (this.editData.roleName.trim() === "") {
                return this.e("Role name is required");
            }

            this.isEditing = true;
            try {
                const res = await this.callApi(
                    "post",
                    "/app/edit_role",
                    this.editData
                );
                this.tags[this.index].roleName = this.editData.roleName;
                this.s("Role has been edited successfully");
                this.editModal = false;
            } catch (e) {
                if (e.response && e.response.status === 422) {
                    this.i(e.response.data.errors.roleName[0]);
                } else {
                    this.swr();
                }
            } finally {
                this.isEditing = false;
            }
        },
        showEditModal(role, index) {
            this.editData = { ...tag }; // Copy the tag object to editData
            this.editModal = true;
            this.index = index;
        },
        async deleteTag() {
            this.isDeleting = true;

            try {
                // Call API to delete the tag
                const res = await this.callApi(
                    "delete",
                    this.getDeleteModalobj.deleteUrl,
                    this.getDeleteModalobj.data
                );

                if (res.status === 200) {
                    // Remove the correct element from the tags array based on the deletingIndex
                    this.tags.splice(this.getDeleteModalobj.deletingIndex, 1);

                    // Show success message
                    this.s("Role has been deleted successfully!");

                    // Close the modal
                    this.$store.commit("setDeleteModal", { isDeleted: true });
                } else {
                    // Handle failure in deleting the tag
                    this.swr("Failed to delete the tag.");
                    this.$store.commit("setDeleteModal", { isDeleted: false });
                }
            } catch (error) {
                this.swr("Error occurred while deleting the tag.");
                this.$store.commit("setDeleteModal", { isDeleted: false });
            } finally {
                // Stop the loading spinner
                this.isDeleting = false;
            }
        },
        showDeletingModal(tag, i) {
            // Prepare deleteModal object to show the modal and track the element
            const deleteModalobj = {
                showDeleteModal: true,
                deleteUrl: "/app/delete_role", // Your delete URL
                data: tag, // Pass the tag object to delete
                deletingIndex: i, // Track the correct index
                isDeleted: false
            };

            // Commit to Vuex store to update the delete modal state
            this.$store.commit("setDeletingModalObj", deleteModalobj);
        }
    },
    async created() {
        try {
            const res = await this.callApi("get", "/app/get_role");
            this.tags = res.data;
        } catch (e) {
            this.e();
        }
    },
    components: {
        deleteModal
    },
    computed: {
        ...mapGetters(["getDeleteModalobj"])
    },
    watch: {
        getDeleteModalobj(obj) {
            if (obj.isDeleted) {
                this.tags.splice(obj.deletingIndex, 1); // Remove the correct element
            }
        }
    }
};
</script>

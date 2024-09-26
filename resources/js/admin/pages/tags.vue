<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
                <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                    <p class="_title0">
                        Tags
                        <Button @click="addModal = true" v-if="isWritePermitted"> 
                            <Icon type="md-add" /> Add tag
                        </Button>
                    </p>

                    <div class="_overflow _table_div">
                        <table class="_table">
                            <!-- TABLE TITLE -->
                            <tr>
                                <th>ID</th>
                                <th>Tag name</th>
                                <th>Created at</th>
                                <th>Action</th>
                            </tr>
                            <!-- ITEMS -->
                            <tr v-for="(tag, i) in tags" :key="tag.id" v-if="tags.length">
                                <td>{{ tag.id }}</td>
                                <td class="_table_name">{{ tag.tagName }}</td>
                                <td>{{ tag.created_at }}</td>
                                <td>
                                    <Button size="small" type="info" @click="showEditModal(tag, i)" v-if="isUpdatePermitted">
                                        Edit
                                    </Button>
                                    <Button type="error" size="small" @click="showDeletingModal(tag, i)" :loading="tag.isDeleting" v-if="isDeletePermitted">
                                        Delete
                                    </Button>
                                </td>
                            </tr>
                        </table>
                        <div class="mt-3">
                            <Button @click="goToPreviousPage" :disabled="currentPage <= 1">Previous</Button>
                            <Button @click="goToNextPage" :disabled="currentPage >= totalPages">Next</Button>
                        </div>
                        <p class="mt-3">Current Page: {{ currentPage }}</p>
                    </div>
                </div>

                <Modal v-model="addModal" title="Add tag" :mask-closable="false" :closable="false">
                    <Input v-model="data.tagName" placeholder="Enter something..." style="width: 300px" />
                    <div slot="footer">
                        <Button type="default" @click="addModal = false">Close</Button>
                        <Button type="primary" @click="addTag" :disabled="isAdding" :loading="isAdding">
                            {{ isAdding ? "Adding.." : "Add tag" }}
                        </Button>
                    </div>
                </Modal>

                <!-- Edit Modal -->
                <Modal v-model="editModal" title="Edit tag" :mask-closable="false" :closable="false">
                    <Input v-model="editData.tagName" placeholder="Edit tag name" style="width: 300px" />
                    <div slot="footer">
                        <Button type="default" @click="editModal = false">Close</Button>
                        <Button type="primary" @click="editTag" :disabled="isEditing" :loading="isEditing">
                            {{ isEditing ? "Editing.." : "Edit tag" }}
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
            data: { tagName: "" },
            addModal: false,
            editModal: false,
            isAdding: false,
            isEditing: false,
            isDeleting: false, // Added this property
            tags: [],
            editData: { id: null, tagName: "" },
            index: -1,

            totalRows: 0,
            perPage: 5,
            currentPage: 1,
            totalPages: 0,
        };
    },
    methods: {
        async addTag() {
            if (this.data.tagName.trim() === "") {
                return this.e("Tag name is required");
            }

            this.isAdding = true;
            try {
                const res = await this.callApi("post", "/app/tags", this.data);
                this.tags.unshift(res.data);
                this.s("Tag added successfully");
                this.addModal = false;
                this.data.tagName = "";
            } catch (e) {
                if (e.response && e.response.status === 422) {
                    this.i(e.response.data.errors.tagName[0]);
                } else {
                    this.swr();
                }
            } finally {
                this.isAdding = false;
            }
        },
        async editTag() {
            if (this.editData.tagName.trim() === "") {
                return this.e("Tag name is required");
            }

            this.isEditing = true;
            try {
                const res = await this.callApi(
                    "put",
                    `/app/tags/${this.editData.id}`,
                    this.editData
                );
                this.tags[this.index].tagName = this.editData.tagName;
                this.s("Tag has been edited successfully");
                this.editModal = false;
            } catch (e) {
                if (e.response && e.response.status === 422) {
                    this.i(e.response.data.errors.tagName[0]);
                } else {
                    this.swr();
                }
            } finally {
                this.isEditing = false;
            }
        },
        showEditModal(tag, index) {
            this.editData = { ...tag }; // Copy the tag object to editData
            this.editModal = true;
            this.index = index;
        },
        async deleteTag() {
            this.isDeleting = true;

            try {
                const res = await this.callApi(
                    "delete",
                    this.getDeleteModalobj.deleteUrl,
                    this.getDeleteModalobj.data
                );

                if (res.status === 200) {
                    this.tags.splice(this.getDeleteModalobj.deletingIndex, 1);
                    this.s("Tag has been deleted successfully!");
                    this.$store.commit("setDeleteModal", { isDeleted: true });
                } else {
                    this.swr("Failed to delete the tag.");
                    this.$store.commit("setDeleteModal", { isDeleted: false });
                }
            } catch (error) {
                this.swr("Error occurred while deleting the tag.");
                this.$store.commit("setDeleteModal", { isDeleted: false });
            } finally {
                this.isDeleting = false;
            }
        },
        showDeletingModal(tag, i) {
            const deleteModalObj = {
                showDeleteModal: true,
                deleteUrl: `app/tags/${tag.id}`,
                data: tag,
                deletingIndex: i,
                isDeleted: false,
            };
            this.$store.commit("setDeletingModalObj", deleteModalObj);
        },
        async fetchTags() {
            
            try {
                const res = await this.callApi(
                    "get",
                    `/app/tags?page=${this.currentPage}&per_page=${this.perPage}`
                );
                this.tags = res.data.data;
                this.totalRows = res.data.total;
                this.totalPages = res.data.last_page;
            } catch (e) {
                this.e();
            }
        },

        async goToPreviousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                await this.fetchTags();
            }
        },
        async goToNextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                await this.fetchTags();
            }
        },
    },
    async created() {
        await this.fetchTags();
    },
    components: {
        deleteModal,
    },
    computed: {
        ...mapGetters(["getDeleteModalobj"]),
    },
    watch: {
        getDeleteModalobj(obj) {
            if (obj.isDeleted) {
                this.tags.splice(obj.deletingIndex, 1);
            }
        },
    },
};
</script>

<template>
    <div>
      <div class="content">
        <div class="container-fluid">
          <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
          <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
            <p class="_title0">
              Tags
              <Button @click="addModal = true"><Icon type="md-add" /> Add tag</Button>
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
                <!-- TABLE TITLE -->
  
                <!-- ITEMS -->
                <tr v-for="(tag, i) in tags" :key="i" v-if="tags.length">
                  <td>{{ tag.id }}</td>
                  <td class="_table_name">{{ tag.tagName }}</td>
                  <td>{{ tag.created_at }}</td>
                  <td>
                    <Button size="small" type="info" @click="showEditModal(tag, i)">Edit</Button>
                    <Button type="error" size="small" @click="showDeletingModal(tag, i)" :loading="tag.isDeleting">Delete</Button>
                  </td>
                </tr>
                <!-- ITEMS -->
              </table>
            </div>
          </div>
  
          <!-- Add Modal -->
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
          <Modal v-model="showDeleteModal" width="360">
            <p slot="header" style="color:#f60;text-align:center">
              <Icon type="ios-information-circle"></Icon>
              <span>Delete confirmation</span>
            </p>
            <div style="text-align:center">
              <p>Are you sure you want to delete this tag?</p>
            </div>
            <div slot="footer">
              <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteTag">
                Delete
              </Button>
            </div>
          </Modal>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        data: {
          tagName: ""
        },
        addModal: false,
        editModal: false,
        isAdding: false,
        isEditing: false,
        tags: [],
        editData: {
          id: null,
          tagName: ""
        },
        index: -1,
        showDeleteModal: false,
        isDeleting: false,
        deleteItem: {},
        deletingIndex: -1,
      };
    },
    methods: {
      async addTag() {
        if (this.data.tagName.trim() === "") {
          return this.e("Tag name is required");
        }
  
        this.isAdding = true;
        const res = await this.callApi("post", "/app/create_tag", this.data);
  
        try {
          this.tags.unshift(res.data);
          this.s("Tag added successfully");
          this.addModal = false;
          this.data.tagName = "";
        } catch (e) {
          if (res.status === 422 && res.data.errors.tagName) {
            this.i(res.data.errors.tagName[0]);
          } else {
            this.swr();
          }
        }
  
        this.isAdding = false;
      },
      async editTag() {
        if (this.editData.tagName.trim() === "") {
          return this.e("Tag name is required");
        }
  
        this.isEditing = true;
        const res = await this.callApi("post", "/app/edit-tag", this.editData);
  
        try {
          this.tags[this.index].tagName = this.editData.tagName;
          this.s("Tag has been edited successfully");
          this.editModal = false;
        } catch (e) {
          if (res.status === 422 && res.data.errors.tagName) {
            this.i(res.data.errors.tagName[0]);
          } else {
            this.swr();
          }
        }
  
        this.isEditing = false;
      },
      showEditModal(tag, index) {
        this.editData = { ...tag }; // Copy the tag object to editData
        this.editModal = true;
        this.index = index;
      },
      async deleteTag() {
        this.isDeleting = true;
        const res = await this.callApi("post", "/app/delete_tag",this.deleteItem);
  
        try {
          this.tags.splice(this.deletingIndex, 1);
          this.s("Tag has been deleted successfully!");
          this.showDeleteModal = false;
        } catch (e) {
          this.swr();
        }
  
        this.isDeleting = false;
      },
      showDeletingModal(tag, i) {
        this.deleteItem = tag;
        this.deletingIndex = i;
        this.showDeleteModal = true;
      }
    },
    async created() {
      try {
        const res = await this.callApi("get", "/app/get-tags");
        this.tags = res.data;
      } catch (e) {
        this.e();
      }
    }
  };
  </script>
  
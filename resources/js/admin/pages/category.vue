<template>
  <div>
    <div class="content">
      <div class="container-fluid">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
          <p class="_title0">
            Category
            <Button @click="addModal = true"  v-if="isWritePermitted"><Icon type="md-add" /> Add category</Button>
          </p>

          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>ID</th>
                <th>Icon image</th>
                <th>Category name</th>
                <th>Created at</th>
                <th>Action</th>
              </tr>
              <!-- TABLE TITLE -->

              <!-- ITEMS -->
              <tr v-for="(category, i) in categoryList" :key="i" v-if="categoryList.length">
                <td>{{ category.id }}</td>
                <td class="table_image">
                  <img :src="category.IconImage" />
                </td>
                <td class="_table_name">{{ category.categoryName }}</td>
                <td>{{ category.created_at }}</td>
                <td>
                  <Button size="small" type="info" @click="showEditModal(category, i)" v-if="isUpdatePermitted">Edit</Button>
                  <Button type="error" size="small" @click="showDeletingModal(category, i)" :loading="category.isDeleting" v-if="isDeletePermitted">Delete</Button>
                </td>
              </tr>
              <!-- ITEMS -->
            </table>
          </div>
        </div>

        <!-- Add Modal -->
        <Modal v-model="addModal" title="Add category" :mask-closable="false" :closable="false">
          <Input v-model="data.categoryName" placeholder="Add category name" style="width: 300px" />
          <div class="space"></div>
          <Upload
            ref="uploads"
            type="drag"
            :headers="{'x-csrf-token': token, 'x-requested-with': 'XMLHttpRequest'}"
            :on-success="handleSuccess"
            :on-error="handleError"
            :format="['jpg', 'jpeg', 'png']"
            :on-format-error="handleFormatError"
            :max-size="2048"
            :on-exceeded-size="handleMaxSize"
            action="app/upload">
            <div style="padding: 20px 0">
              <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
              <p>Click or drag files here to upload</p>
            </div>
          </Upload>

          <div class="demo-upload-list" v-if="data.IconImage">
            <img :src="`${data.IconImage}`">
            <div class="demo-upload-list-cover">
              <Icon type="ios-trash-outline" @click="deleteImage"></Icon>
            </div>
          </div>

          <div slot="footer">
            <Button type="default" @click="addModal = false">Close</Button>
            <Button type="primary" @click="addCategory" :disabled="isAdding" :loading="isAdding">
              {{ isAdding ? "Adding.." : "Add tag" }}
            </Button>
          </div>
        </Modal>

        <Modal v-model="editModal" title="Edit Category" :mask-closable="false" :closable="false">
            <Input v-model="editData.categoryName" placeholder="Edit category name" style="width: 300px" />
            <div class="space"></div>
            <Upload v-show="isIconImageNew"
              ref="editDataUploads"
              type="drag"
              :headers="{'x-csrf-token': token, 'x-requested-with': 'XMLHttpRequest'}"
              :on-success="handleSuccess"
              :on-error="handleError"
              :format="['jpg', 'jpeg', 'png']"
              :on-format-error="handleFormatError"
              :max-size="2048"
              :on-exceeded-size="handleMaxSize"
              action="app/upload">
              <div style="padding: 20px 0">
                <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
                <p>Click or drag files here to upload</p>
              </div>
            </Upload>

            <div class="demo-upload-list" v-if="editData.IconImage">
              <img :src="`${editData.IconImage}`" alt="Uploaded image">
              <div class="demo-upload-list-cover">
                <Icon type="ios-trash-outline" @click="deleteImage(false)"></Icon>
              </div>
            </div>

            <div slot="footer">
              <Button type="default" @click="closeEditModal">Close</Button>
              <Button type="primary" @click="editCategory" :disabled="isEditing" :loading="isEditing">
                {{ isEditing ? "Editing.." : "Edit category" }}
              </Button>
            </div>
        </Modal>
          <deleteModal></deleteModal>
      </div>
    </div>
  </div>
</template>

<script>
import deleteModal from '../components/deleteModal.vue'
import { mapGetters } from 'vuex';
export default {
  data() {
    return {
      data: {
        IconImage: "",
        categoryName: ""  // Changed categoryName to tagName to be consistent
      },
      addModal: false,
      editModal: false,
      isAdding: false,
      isEditing: false,
      categoryList: [],
      editData: {
        IconImage: "",
        categoryName: ""
      },
      index: -1,
      showDeleteModal: false,
      isDeleting: false,
      deleteItem: {},
      deletingIndex: -1,
      token: "",
      isIconImageNew: false,
      isEditingItem: false, 
      websiteSettings:[],
    };
  },
  methods: {
    async addCategory() {
      

      if (this.data.categoryName.trim() === "") {
        return this.e("Category name is required");
      }
      if ( this.data.IconImage.trim() === "") {
        return this.e("Category name is required");
      }
      this.data.IconImage=`${this.data.IconImage}`
      this.isAdding = true;
      const res = await this.callApi("post", "/app/create_category", this.data);

      try {
        
          this.categoryList.unshift(res.data);
          this.s("Category added successfully" );
          this.addModal = false;          
          this.data.categoryName = ""; // Reset the image after adding
          this.data.IconImage="";
        
      } catch (e) {
        if (res.status === 422) {
          if(res.data.errors.categoryName){
            this.e(res.data.errors.categoryName[0]);
          }
          if(res.errors.IconImage){
            this.e(res.data.errors.IconImage[0]);
          }
        } else{
          this.swr()
        }
      }


      this.isAdding = false;
    },


    async editCategory() {
      if (this.editData.categoryName.trim() === "") {
        return this.e("Category name is required");
      }
      if (this.editData.IconImage.trim() === "") {
        return this.e("Category image is required");
      }

      // Check if a new image was uploaded
      if (this.isIconImageNew && this.editData.IconImage !== this.categoryList[this.index].IconImage) {
        // Delete the old image
        await this.deleteImage(false);
      }

      try {
        const res = await this.callApi("post", "/app/edit_category", this.editData);
        if (res.status === 200) {
          this.categoryList[this.index].categoryName = this.editData.categoryName;
          this.categoryList[this.index].IconImage = this.editData.IconImage; // Update the image in the list
          this.s("Category has been edited successfully");
          this.editModal = false;
        }
      } catch (e) {
        if (res.status === 422) {
          if (res.data.errors.categoryName) {
            this.e(res.data.errors.categoryName[0]);
          }
          if (res.data.errors.IconImage) {
            this.e(res.data.errors.IconImage[0]);
          }
        } else {
          this.swr();
        }
      }
    }

    ,
    showEditModal(category, index) {
      this.editData = category
      this.editModal = true;
      this.index = index;
      this.isEditingItem=true
    },
   
    showDeletingModal(category, i) {
      const deleteModalobj={
            showDeleteModal:true,
            deleteUrl:'/app/delete_category',
            data:category,
            deletingIndex: i,
            isDeleted:false,
      }
      this.$store.commit("setDeletingModalObj", deleteModalobj);
   
    },
   handleSuccess(res) {
      res=`/uploads/${res}`
      if(this.isEditingItem){
        return this.editData.IconImage=res;
      }
      this.data.IconImage = res; // Make sure the response contains the file path or necessary data
    },
    handleError(error) {
      this.e("File upload failed. Please try again.");
    },
    handleFormatError(file) {
      this.e( `File format of ${file.name} is incorrect. Please select jpg or png.`);
    },
    handleMaxSize(file) {
      this.e(`File ${file.name} is too large, no more than 2MB.`);
    },

    async fetchTags() {
      this.token = window.Laravel.csrfToken;
      const res = await this.callApi("get", "/app/get-category");
      try {
        this.categoryList = res.data;
      } catch (e) {
        this.$Notice.error({ title: "Error", desc: "Failed to fetch tags." });
      }
    },
    async deleteImage(isAdd = true) {
      let image;
      if (!isAdd) {
        this.isIconImageNew = true;
        image = this.editData.iconImage; // `IconImage` o'rniga `iconImage`
        this.editData.iconImage = ""; // `IconImage` o'rniga `iconImage`
        this.$refs.editDataUploads.clearFiles();
      } else {
        image = this.data.iconImage; // `IconImage` o'rniga `iconImage`
        this.data.iconImage = ""; // `IconImage` o'rniga `iconImage`
        this.$refs.uploads.clearFiles();
      }
      const res = await this.callApi("post", "app/delete_image", { imageName: image });
      if (res.status != 200) {
        this.data.iconImage = image; // `IconImage` o'rniga `iconImage`
        this.swr();
      }
    },
    closeEditModal(){
      this.isEditingItem=false
      this.editModal=false
    }
  },
  async created() {
    this.token = window.Laravel.csrfToken;
    await this.fetchTags();
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
        this.categoryList.splice(obj.deletingIndex, 1); // Remove the correct element
      }
    }
  }

};
</script>

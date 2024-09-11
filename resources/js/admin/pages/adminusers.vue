<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <!-- TABLE -->
                <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                    <p class="_title0">
                        Users
                        <Button @click="addModal = true"><Icon type="md-add" /> Add User</Button>
                    </p>
                    <div class="_overflow _table_div">
                        <table class="_table">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created at</th>
                                <th>Action</th>
                            </tr>
                            <tr v-for="(user, i) in users" :key="i" v-if="users.length">
                                <td>{{ user.id }}</td>
                                <td class="_table_name">{{ user.fullName }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.created_at }}</td>
                                <td>
                                    <Button size="small" type="info" @click="showEditModal(user, i)">Edit</Button>
                                    <Button type="error" size="small" @click="showDeletingModal(user, i)" :loading="user.isDeleting">Delete</Button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Add Modal -->
                <Modal v-model="addModal" title="Add User" :mask-closable="false" :closable="false">
                    <div class="space">
                        <Input type="text" v-model="data.fullName" placeholder="Full name"/>
                    </div>    
                    <div class="space">
                        <Input type="email" v-model="data.email" placeholder="Email"/>
                    </div>    
                    <div class="space">
                        <Input type="password" v-model="data.password" placeholder="Password"/>
                    </div>
                    <div class="space">
                        <Select v-model="data.role_id" placeholder="Select user type">
                            <Option :value="r.id" v-for="(r,i) in roles" :key="i" v-if="roles.length">{{ r.roleName }}</Option>
                        </Select>
                    </div>
                    <div slot="footer">
                        <Button type="default" @click="addModal = false">Close</Button>
                        <Button type="primary" @click="addAdmin" :disabled="isAdding" :loading="isAdding">
                            {{ isAdding ? "Adding.." : "Add User" }}
                        </Button>
                    </div>
                </Modal>

                <!-- Edit Modal -->
                <Modal v-model="editModal" title="Edit Admin" :mask-closable="false" :closable="false">
                    <div class="space">
                        <Input type="text" v-model="data.fullName" placeholder="Full name"/>
                    </div>    
                    <div class="space">
                        <Input type="email" v-model="data.email" placeholder="Email"/>
                    </div>    
                    <div class="space">
                        <Input type="password" v-model="data.password" placeholder="Password"/>
                    </div>
                    <div class="space">
                        <Select v-model="data.role_id" placeholder="Select user type">
                            <Option :value="r.id" v-for="(r,i) in roles" :key="i" v-if="roles.length">{{ r.roleName }}</Option>
                        </Select>
                    </div>
                    <div slot="footer">
                        <Button type="default" @click="editModal = false">Close</Button>
                        <Button type="primary" @click="editAdmin" :disabled="isEditing" :loading="isEditing">
                            {{ isEditing ? "Editing.." : "Edit Admin" }}
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
            data: { 
                fullName: "",
                email: "",
                password: "",   
                role_id: null
             },
            addModal: false,
            editModal: false,
            isAdding: false,
            isEditing: false,
            isDeleting: false,
            users: [],
            roles:[],
            index: -1
        };
    },
    methods: {
        async addAdmin() {
            if (this.data.fullName.trim() === ""){return this.e("Full name is required");}
            if (this.data.email.trim() === ""){return this.e("Email is required");}
            if (this.data.password.trim() === ""){return this.e("Password is required");}
            if (!this.data.role_id){return this.e("User type is required");}
            
            const res = await this.callApi("post", "/app/create_user", this.data);
            try {
                this.users.unshift(res.data); // Add new user to the users array
                this.s("User has been added successfully");
                this.addModal = false;
                this.data = { fullName: "", email: "", password: ""};
            } catch (e) {
                if (res.status == 422) {
                    console.log(res.data.errors);
                } else {
                    this.swr();
                }
            }
        },
        async editAdmin(){
			if(this.data.fullName.trim()=='') return this.e('Full name is required')
            if(this.data.email.trim()=='') return this.e('Email is required')
            if(!this.data.role_id) return this.e('User type  is required')
			const res = await this.callApi('post', 'app/edit_user', this.data)
			if(res.status===200){
				this.users[this.index] = this.data
				this.s('User has been edited successfully!')
				this.data = false
				
			}else{
				if(res.status==422){
					for(let i in res.data.errors){
                        this.e(res.data.errors[i][0])
                    }
					
				}else{
					this.swr()
				}
				
			}

		},
        async deleteUser() {
            this.isDeleting = true;
            try {
                const res = await this.callApi("post", "/app/delete_user", { id: this.getDeleteModalobj.data.id });
                if (res.status === 200) {
                    this.users.splice(this.getDeleteModalobj.deletingIndex, 1); // Remove the user from the users array
                    this.s("User has been deleted successfully!");
                    this.$store.commit("setDeleteModal", { isDeleted: true });
                }
            } catch (e) {
                this.swr();
            } finally {
                this.isDeleting = false;
            }
        },
        showDeletingModal(user, i) {
            const deleteModalobj = {
                showDeleteModal: true,
                deleteUrl: "/app/delete_user", 
                data: user,
                deletingIndex: i,
                isDeleted: false
            };
            this.$store.commit("setDeletingModalObj", deleteModalobj);
        },showEditModal(user, index){
			let obj = {
				id : user.id, 
				fullName : user.fullName, 
				email : user.email, 
			}
			this.data = obj
			this.editModal = true
			this.index = index
		}, 
    },
    async created() {
        const [res,resRole]=await Promise.all([
        this.callApi("get", "/app/get_users"),
        this.callApi("get", "/app/get_role")
        ]);
        
        try {
            this.users = res.data;
        } catch (e) {
            this.swr();
        }

        try{
            this.roles=resRole.data;
        }catch(e){
            this.swr();
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
                this.users.splice(obj.deletingIndex, 1); // Remove the correct element
            }
        }
    }
};
</script>

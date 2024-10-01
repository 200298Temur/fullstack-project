import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

import fisrtPage from './components/pages/myFirstVuePage.vue'
import newRoutePage from './components/pages/newRoutePage.vue'
import hooks from './components/pages/home.vue'
import methods from './components/pages/basic/methods.vue'
import usecom from './vuex/usecom.vue'

import home from './components/pages/home.vue'
import tags from './admin/pages/tags.vue'
import category from './admin/pages/category.vue'
import adminuser from './admin/pages/adminusers.vue'
import login from './admin/pages/login.vue'
import role from './admin/pages/role.vue'
import assignRole from './admin/pages/assignRole.vue'
import createBlog from './admin/pages/createBlog'
import blogs from './admin/pages/blog.vue'
import editblog from './admin/pages/editblog.vue'
import notfound from './admin/pages/notfound.vue'

import { component } from 'vue/types/umd'
const routes = [
    {
        path: '/testvuex',
        component: usecom
    },
    {
        path: '/',
        component: home,
        name: 'home'  // Use string 'home' instead of home component
    },
    {
        path: '/tags',
        component: tags,
        name: 'tags'  // Use string 'tags' instead of tags component
    },
    {
        path: '/category',
        component: category,
        name: 'category'  // Use string 'category'
    },
    {
        path: '/createBlog',
        component: createBlog,
        name:'createBlog'
    },
    {
        path: '/blogs',
        component: blogs,
        name:'blogs'
    },
    {
        path: '/adminuser',
        component: adminuser,
        name: 'adminuser'  // Use string 'adminuser'
    },
    {
        path: '/login',
        component: login,
        name: 'login'  // Use string 'login'
    },
    {
        path: '/role',
        component: role,
        name: 'role'  // Use string 'role'
    },
    {
        path: '/assignRole',
        component: assignRole,
        name: 'assignRole'  // Use string 'assignRole'
    },
    {
        path: '/editblog/:id',
        component: editblog,
        name: 'editblog'  
    },
    {
        path:'*',
        component:notfound,
        name:'notfound'
    },  
    {
        path: '/my-new-vue-route',
        component: fisrtPage
    },
    {
        path: '/new-route',
        component: newRoutePage
    },
    {
        path: '/hooks',
        component: hooks
    },
    {
        path: '/methods',
        component: methods
    }
]

export default new Router({
    mode: 'history',
    routes
})

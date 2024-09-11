import Vue from  'vue'
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


const routes=[
    {
        path: '/testvuex',
        component: usecom
    },
    {
        path: '/',
        component: home
    },
    {
        path: '/tags',
        component: tags,
    },
    {
        path: '/category',
        component: category,
    },
    {
        path: '/adminuser',
        component: adminuser,
    },
    {
        path: '/login',
        component: login,
    },
    {
        path: '/role',
        component: role,
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
        component:methods
    }
]

export default new Router({
    mode:'history',
    routes
})
import Vue from  'vue'
import Router from 'vue-router'


Vue.use(Router)
import fisrtPage from './components/pages/myFirstVuePage'
import newRoutePage from './components/pages/newRoutePage'
import hooks from './components/pages/home'
import methods from './components/pages/basic/methods.vue'

import home from './components/pages/home'
import tags from './admin/pages/tags'
import category from './admin/pages/category'


const routes=[
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
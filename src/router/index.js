import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/about',
    name: 'about',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  },
  {
    path: '/add_customer',
    name: 'add_customer',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/Add_Customer.vue')
  },
  {
    path: '/customer',
    name: 'customer',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/CustomerView.vue')
  },
  {
    path: '/employee',
    name: 'employee',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/EmployeeView.vue')
  },
  {
    path: '/add_employee',
    name: 'add_employee',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/Add_employee.vue')
  },
{
    path: '/add_contacts',
    name: 'add_contacts',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/Add_Contacts.vue')
  },
  {
    path: '/contacts',
    name: 'contacts',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/Contacts.vue')
  }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export default router

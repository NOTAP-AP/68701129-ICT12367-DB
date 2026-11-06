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
    path: '/showproduct',
    name: 'products',
    component: () => import('../views/ProductView.vue')
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
    component: () => import('../views/Contacts.vue')
  },
  {
    path: '/contacts_crud',
    name: 'contacts_crud',
    component: () => import('../views/Contacts_crud.vue')
  },
{
    path: '/customer_crud',
    name: 'customer_crud',
    meta: { requiresAuth: true },
    component: () => import(/* webpackChunkName: "about" */ '../views/Customer_crud.vue')
  },
{
    path: '/employee_crud',
    name: 'employee_crud',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/Employee_crud.vue'),
    meta: { requiresAuth: true }
  },
{
    path: '/product',
    name: 'product',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/Product.vue')
  },

  {
    path: '/asset',
    name: 'asset',
    
    component: () => import(/* webpackChunkName: "about" */ '../views/Asset.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/Add_Customer.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/Login.vue')
  },
]


const router = createRouter({
  history: createWebHashHistory(),
  routes
})
// ตรวจสอบสิทธิ์ก่อนเปลี่ยนหน้า
router.beforeEach((to, from, next) => {

  // ตรวจสอบสถานะ Login จาก localStorage
  const isLoggedIn = localStorage.getItem('adminLogin') === 'true'

  // ถ้าหน้านั้นต้อง Login แต่ยังไม่ได้ Login
  if (to.meta.requiresAuth && !isLoggedIn) {
    next('/login')
  }

  // ถ้า Login แล้ว ไม่ต้องกลับไปหน้า Login
  else if (to.path === '/login' && isLoggedIn) {
    next('/')
  }

  // กรณีอื่น ๆ อนุญาตให้เข้าหน้าได้
  else {
    next()
  }
})

export default router

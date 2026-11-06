<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <router-link class="navbar-brand fw-bold" to="/">Loba Shop</router-link>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"v-if="isLoggedIn">
            <router-link class="nav-link" to="/">Home</router-link>
          </li>
          <li class="nav-item dropdown"v-if="isLoggedIn">
            <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              Customer
            </button>
            <ul class="dropdown-menu">
              <li><router-link class="dropdown-item" :to="{ name: 'customer' }">ข้อมูลลูกค้า</router-link></li>
              <li><router-link class="dropdown-item" :to="{ name: 'customer_crud' }">Customer_CRUD</router-link></li>
            </ul>
          </li>
          <li class="nav-item dropdown"">
            <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              Emloyee
            </button>
            <ul class="dropdown-menu">
              <li><router-link class="dropdown-item" :to="{ name: 'employee' }">ข้อมูลพนักงาน</router-link></li>
              <li><router-link class="dropdown-item" :to="{ name: 'employee_crud' }">Employee CRUD</router-link></li>
              <li><router-link class="dropdown-item" :to="{ name: 'employee_crud' }">Logout</router-link></li>
            </ul>
          </li>

          <li class="nav-item dropdown"v-if="isLoggedIn">
          <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Contact
          </button>
          <ul class="dropdown-menu">
            <li><router-link class="dropdown-item" :to="{ name: 'contacts' }">Contact</router-link></li>
            <li><router-link class="dropdown-item" :to="{ name: 'add_contacts' }">เพิ่มข้อมูล</router-link></li>
            <li><hr class="dropdown-divider"></li>
            <li><router-link class="dropdown-item" :to="{ name: 'contacts_crud' }">Contact CRUD</router-link></li>
          </ul>
        </li>


          <li class="nav-item dropdown"v-if="isLoggedIn">
            <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              Product
            </button>
            <ul class="dropdown-menu">
              <li><router-link class="dropdown-item" :to="{ name: 'product' }">จัดการสินค้า</router-link></li>
              <li><router-link class="dropdown-item" :to="{ name: 'products' }">รายการสินค้า</router-link></li>
            </ul>
          </li>

          
           <li class="nav-item"v-if="isLoggedIn">
            <router-link class="nav-link" to="/asset">Asset</router-link>
          </li>
           <li class="nav-item dropdown" v-if="!isLoggedIn">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                        >
                            Register
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                            <router-link class="dropdown-item" :to="{ name: 'register' }">
                                    Register
                            </router-link>
                            </li>

                            <li>
                            <router-link class="dropdown-item" :to="{ name: 'login' }">
                                    Login
                            </router-link>
                            </li>
                        </ul>
                    </li>

                </ul>

                <!-- ================= User Information ================= -->
                <!-- แสดงเฉพาะเมื่อ Login แล้ว -->
                <div
                    class="d-flex align-items-center ms-lg-3 mt-2 mt-lg-0"
                    v-if="isLoggedIn"
                >
                    <span class="text-white me-3">
                        <i class="bi bi-person-circle"></i>
                        {{ userName }}
                    </span>

                    <button
                        @click="logout"
                        class="btn btn-danger btn-sm"
                    >
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </button>
                </div>

            </div>
        </div>
    </nav>
</template>

<script>
export default {
    name: "Navbar",

    data() {
        return {
            // สถานะ Login
            isLoggedIn: false,

            // ชื่อผู้ใช้งาน
            userName: ""
        };
    },

    // ทำงานเมื่อ Component ถูกสร้าง
    mounted() {
        this.checkLogin();
    },

    methods: {

        // ตรวจสอบสถานะ Login
        checkLogin() {

            this.isLoggedIn =
                localStorage.getItem("adminLogin") === "true";

            if (this.isLoggedIn) {

                try {
                    // อ่านข้อมูลผู้ใช้
                    const userData = localStorage.getItem("user");

                    const user = userData
                        ? JSON.parse(userData)
                        : null;

                    // กำหนดชื่อผู้ใช้
                    this.userName =
                        user?.name || user?.username || "Admin";

                } catch (error) {
                    this.userName = "Admin";
                }

            } else {
                this.userName = "";
            }
        },

        // ออกจากระบบ
        logout() {

            // ลบข้อมูล Login
            localStorage.removeItem("adminLogin");
            localStorage.removeItem("user");

            // รีเซ็ตสถานะ
            this.isLoggedIn = false;
            this.userName = "";

            // กลับหน้า Login
            this.$router.push("/login");
        }
    },

    // ตรวจสอบสถานะใหม่เมื่อเปลี่ยนหน้า
    watch: {
        '$route'() {
            this.checkLogin();
        }
    }
};
</script>



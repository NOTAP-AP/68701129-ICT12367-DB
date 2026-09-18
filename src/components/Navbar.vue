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
          <li class="nav-item">
            <router-link class="nav-link" to="/">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/showproduct">Show Product</router-link>
          </li>
           <li class="nav-item">
            <router-link class="nav-link" to="/Customer">Customer</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/employee">Employee</router-link>
          </li>

          <li class="nav-item dropdown">
          <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Contact
          </button>
          <ul class="dropdown-menu">
            <li><router-link class="dropdown-item" :to="{ name: '/contacts' }">Contact</router-link></li>
            <li><router-link class="dropdown-item" :to="{ name: '/add_contacts' }">เพิ่มข้อมูล</router-link></li>
            <li><hr class="dropdown-divider"></li>
            <li><router-link class="dropdown-item" :to="{ name: '/show_contacts' }">แสดงข้อมูล</router-link></li>
          </ul>

        </li>
           <li class="nav-item">
            <router-link class="nav-link" to="/about">About</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: "Navbar"
}
</script>



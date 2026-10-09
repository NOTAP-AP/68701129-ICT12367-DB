<template>
  <main class="container mt-4">
    <h2 class="mb-3">รายการสินค้า</h2>

    <div v-if="loading" class="text-center py-3">กำลังโหลดข้อมูล...</div>
    <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
    <div v-else-if="products.length === 0" class="alert alert-info">ไม่พบข้อมูลสินค้า</div>
    <div v-else class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>รหัสสินค้า</th>
            <th>ชื่อสินค้า</th>
            <th>รายละเอียด</th>
            <th>ราคา</th>
            <th>คงเหลือ</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.product_id">
            <td>{{ product.product_id }}</td>
            <td>{{ product.product_name }}</td>
            <td>{{ product.description }}</td>
            <td>{{ Number(product.price).toLocaleString('th-TH', { minimumFractionDigits: 2 }) }} บาท</td>
            <td>{{ product.stock }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>
</template>

<script>
import { onMounted, ref } from 'vue'

export default {
  name: 'ProductView',
  setup() {
    const products = ref([])
    const loading = ref(true)
    const error = ref('')

    onMounted(async () => {
      try {
        const response = await fetch('http://localhost/68701129-ICT12367-DB/php.api/show_product.php')
        if (!response.ok) throw new Error('ไม่สามารถดึงข้อมูลสินค้าได้')
        const data = await response.json()
        if (!Array.isArray(data)) throw new Error('ข้อมูลสินค้าที่ได้รับไม่ถูกต้อง')
        products.value = data
      } catch (err) {
        error.value = err.message || 'เกิดข้อผิดพลาดในการโหลดข้อมูลสินค้า'
      } finally {
        loading.value = false
      }
    })

    return { products, loading, error }
  }
}
</script>

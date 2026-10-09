<template>
  <div class="container mt-4">
    <!-- หัวข้อหน้า -->
    <h2 class="mb-3">รายชื่อพนักงาน</h2>
    <div class="text-end mb-3">
      <router-link :to="{ name: 'add_employee' }" class="btn btn-primary">Add+</router-link>
     </div>
    <!-- ตารางแสดงข้อมูลพนักงาน -->
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ลำดับที่</th>        <!-- index -->
          <th>รหัสพนักงาน</th>   <!-- emp_id -->
          <th>ชื่อ</th>            <!-- firstName -->
          <th>นามสกุล</th>        <!-- lastName -->
          <th>เบอร์โทร</th>       <!-- phone -->
          <th>ชื่อผู้ใช้</th>      <!-- username -->
          <th>จัดการ</th>
        </tr>
      </thead>

      <tbody>
        <!-- วนลูปข้อมูลพนักงาน -->
        <tr v-for="(item,index) in customers" :key="item.emp_id">
          <td>{{ index + 1 }}</td>       <!-- แสดงลำดับที่ (เริ่มจาก 1) -->
          <td>{{ item.emp_id }}</td> <!-- รหัสพนักงาน -->
          <td>{{ item.firstName }}</td>   <!-- ชื่อ -->
          <td>{{ item.lastName }}</td>    <!-- นามสกุล -->
          <td>{{ item.phone }}</td>       <!-- เบอร์โทร -->
          <td>{{ item.username }}</td>    <!-- ชื่อผู้ใช้ -->
          <td>
            <button
              class="btn btn-danger btn-sm"
              :disabled="deletingId === item.emp_id"
              @click="deleteEmployee(item)"
            >
              {{ deletingId === item.emp_id ? "กำลังลบ..." : "ลบ" }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Loading: แสดงระหว่างรอข้อมูล -->
    <div v-if="loading" class="text-center">
      <p>กำลังโหลดข้อมูล...</p>
    </div>

    <!-- Error: แสดงเมื่อเกิดข้อผิดพลาด -->
    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>
    <div v-if="success" class="alert alert-success">
      {{ success }}
    </div>
  </div>
</template>

<script>
// import ฟังก์ชันจาก Vue (Composition API)
import { ref, onMounted } from "vue";

export default {
  name: "CustomerList", // ชื่อ component

  setup() {
    // -----------------------------
    // state (ตัวแปร reactive)
    // -----------------------------
    const customers = ref([]); // เก็บข้อมูลลูกค้า (array)
    const loading = ref(true); // สถานะโหลดข้อมูล
    const error = ref(null);   // เก็บ error
    const success = ref(null);
    const deletingId = ref(null);

    // -----------------------------
    // ฟังก์ชันดึงข้อมูลจาก API
    // -----------------------------
    const fetchdata = async () => {
      try {
        error.value = null;
        // เรียก API (PHP)
        const response = await fetch("http://localhost/68701129-ICT12367-DB/php.api/show_employee.php");

        // ตรวจสอบว่าการเรียกสำเร็จหรือไม่
        if (!response.ok) {
          throw new Error("ไม่สามารถดึงข้อมูลได้");
        }

        // แปลง response เป็น JSON
        customers.value = await response.json();

      } catch (err) {
        // ถ้า error ให้เก็บข้อความไว้แสดง
        error.value = err.message;

      } finally {
        // ไม่ว่าจะสำเร็จหรือ error ให้หยุด loading
        loading.value = false;
      }
    };

    const deleteEmployee = async (employee) => {
      if (!window.confirm(`ยืนยันการลบพนักงาน ${employee.firstName} ${employee.lastName} หรือไม่?`)) {
        return;
      }

      deletingId.value = employee.emp_id;
      error.value = null;
      success.value = null;

      try {
        const response = await fetch("http://localhost/68701129-ICT12367-DB/php.api/delete_employee.php", {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ emp_id: employee.emp_id })
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
          throw new Error(result.message || "ไม่สามารถลบข้อมูลพนักงานได้");
        }

        customers.value = customers.value.filter((item) => item.emp_id !== employee.emp_id);
        success.value = result.message;
      } catch (err) {
        error.value = err.message;
      } finally {
        deletingId.value = null;
      }
    };

    // -----------------------------
    // lifecycle: ทำงานเมื่อ component โหลดเสร็จ
    // -----------------------------
    onMounted(() => {
      fetchdata(); // เรียก API ทันที
    });

    // -----------------------------
    // return ค่าไปใช้ใน template
    // -----------------------------
    return {
      customers,
      loading,
      error,
      success,
      deletingId,
      deleteEmployee
    };
  }
};
</script>
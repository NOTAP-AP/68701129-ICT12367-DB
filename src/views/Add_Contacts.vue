<template>
  <div class="container mt-4 col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h2 class="text-center mb-4">เพิ่มข้อมูลผู้ติดต่อ</h2>
        <form @submit.prevent="addData">
          <div class="mb-3">
            <label for="fullname" class="form-label">ชื่อ-นามสกุล</label>
            <input id="fullname" v-model.trim="contact.fullname" class="form-control" maxlength="100" required />
          </div>
          <div class="mb-3">
            <label for="subject" class="form-label">หัวข้อที่ต้องการติดต่อ</label>
            <input id="subject" v-model.trim="contact.subject" class="form-control" maxlength="255" required />
          </div>
          <div class="mb-3">
            <label for="detail" class="form-label">รายละเอียด</label>
            <textarea id="detail" v-model.trim="contact.detail" class="form-control" rows="4" required></textarea>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">อีเมล</label>
            <input id="email" v-model.trim="contact.email" type="email" class="form-control" maxlength="255" required />
          </div>
          <div class="d-flex justify-content-center gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              {{ submitting ? 'กำลังบันทึก...' : 'บันทึก' }}
            </button>
            <button type="button" class="btn btn-secondary" :disabled="submitting" @click="resetForm">ยกเลิก</button>
          </div>
        </form>

        <div v-if="message" class="alert mt-3 mb-0" :class="success ? 'alert-success' : 'alert-danger'" role="status">
          {{ message }}
        </div>
      </div>
    </div>
  </div>
</template>


<script>
export default {
  data() {
    return {
      contact: {
        fullname: "",
        subject: "",
        detail: "",
        email: ""
      },
      message: "",
      success: false,
      submitting: false
    };
  },
  methods: {
    resetForm() {
      this.contact = { fullname: "", subject: "", detail: "", email: "" };
      this.message = "";
      this.success = false;
    },
    async addData() {
      if (this.submitting) return;

      this.submitting = true;
      this.message = "";
      try {
        const res = await fetch("http://localhost/68701129-ICT12367-DB/php.api/add_contacts.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.contact)
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) {
          throw new Error(data.message || data.error || "ไม่สามารถบันทึกข้อมูลได้");
        }

        this.success = true;
        this.message = data.message || "เพิ่มข้อมูลเรียบร้อย";
        this.contact = { fullname: "", subject: "", detail: "", email: "" };

      } catch (err) {
        this.success = false;
        this.message = "เกิดข้อผิดพลาด: " + err.message;
      } finally {
        this.submitting = false;
      }
    }
  }
}
</script>

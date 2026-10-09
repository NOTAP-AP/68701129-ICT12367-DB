<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="mb-0">จัดการข้อมูลผู้ติดต่อ</h2>
      <button class="btn btn-primary" @click="openAddModal">เพิ่มข้อมูล</button>
    </div>

    <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>
    <div v-if="success" class="alert alert-success" role="status">{{ success }}</div>
    <div v-if="loading" class="text-center py-3">กำลังโหลดข้อมูล...</div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>รหัส</th>
            <th>ชื่อ-นามสกุล</th>
            <th>หัวข้อ</th>
            <th>รายละเอียด</th>
            <th>อีเมล</th>
            <th>จัดการ</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="contact in contacts" :key="contact.contact_id">
            <td>{{ contact.contact_id }}</td>
            <td>{{ contact.fullname }}</td>
            <td>{{ contact.subject }}</td>
            <td class="contact-detail">{{ contact.detail }}</td>
            <td>{{ contact.email }}</td>
            <td class="text-nowrap">
              <button class="btn btn-warning btn-sm me-2" @click="openEditModal(contact)">แก้ไข</button>
              <button class="btn btn-danger btn-sm" :disabled="deletingId === contact.contact_id" @click="deleteContact(contact)">
                {{ deletingId === contact.contact_id ? 'กำลังลบ...' : 'ลบ' }}
              </button>
            </td>
          </tr>
          <tr v-if="!loading && contacts.length === 0">
            <td colspan="6" class="text-center">ยังไม่มีข้อมูลผู้ติดต่อ</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div id="contactModal" ref="modalElement" class="modal fade" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditMode ? 'แก้ไขข้อมูลผู้ติดต่อ' : 'เพิ่มข้อมูลผู้ติดต่อ' }}</h5>
            <button type="button" class="btn-close" aria-label="ปิด" @click="closeModal"></button>
          </div>
          <form @submit.prevent="saveContact">
            <div class="modal-body">
              <div class="mb-3">
                <label for="contact-fullname" class="form-label">ชื่อ-นามสกุล</label>
                <input id="contact-fullname" v-model.trim="editContact.fullname" class="form-control" maxlength="100" required>
              </div>
              <div class="mb-3">
                <label for="contact-subject" class="form-label">หัวข้อที่ต้องการติดต่อ</label>
                <input id="contact-subject" v-model.trim="editContact.subject" class="form-control" maxlength="255" required>
              </div>
              <div class="mb-3">
                <label for="contact-detail" class="form-label">รายละเอียด</label>
                <textarea id="contact-detail" v-model.trim="editContact.detail" class="form-control" rows="4" required></textarea>
              </div>
              <div class="mb-3">
                <label for="contact-email" class="form-label">อีเมล</label>
                <input id="contact-email" v-model.trim="editContact.email" type="email" class="form-control" maxlength="255" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" :disabled="saving" @click="closeModal">ยกเลิก</button>
              <button type="submit" class="btn btn-success" :disabled="saving">
                {{ saving ? 'กำลังบันทึก...' : (isEditMode ? 'บันทึกการแก้ไข' : 'เพิ่มข้อมูล') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount } from "vue";

const API_URL = "http://localhost/68701129-ICT12367-DB/php.api/contacts_crud.php";
const emptyContact = () => ({ fullname: "", subject: "", detail: "", email: "" });

export default {
  name: "ContactsCrud",
  setup() {
    const contacts = ref([]);
    const loading = ref(true);
    const saving = ref(false);
    const deletingId = ref(null);
    const error = ref("");
    const success = ref("");
    const editContact = ref(emptyContact());
    const isEditMode = ref(false);
    const modalElement = ref(null);
    let contactModal = null;

    const fetchContacts = async () => {
      error.value = "";
      try {
        const response = await fetch(API_URL);
        const result = await response.json();
        if (!response.ok || !result.success) {
          throw new Error(result.message || "ไม่สามารถโหลดข้อมูลผู้ติดต่อได้");
        }
        contacts.value = result.data;
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    const openAddModal = () => {
      isEditMode.value = false;
      editContact.value = emptyContact();
      success.value = "";
      contactModal.show();
    };

    const openEditModal = (contact) => {
      isEditMode.value = true;
      editContact.value = {
        contact_id: contact.contact_id,
        fullname: contact.fullname,
        subject: contact.subject,
        detail: contact.detail,
        email: contact.email
      };
      success.value = "";
      contactModal.show();
    };

    const closeModal = () => contactModal?.hide();

    const saveContact = async () => {
      saving.value = true;
      error.value = "";
      success.value = "";
      try {
        const response = await fetch(API_URL, {
          method: isEditMode.value ? "PUT" : "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(editContact.value)
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
          throw new Error(result.message || "ไม่สามารถบันทึกข้อมูลได้");
        }
        closeModal();
        success.value = result.message;
        await fetchContacts();
      } catch (err) {
        error.value = err.message;
      } finally {
        saving.value = false;
      }
    };

    const deleteContact = async (contact) => {
      if (!window.confirm(`ยืนยันการลบข้อมูลของ ${contact.fullname} หรือไม่?`)) return;
      deletingId.value = contact.contact_id;
      error.value = "";
      success.value = "";
      try {
        const response = await fetch(API_URL, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ contact_id: contact.contact_id })
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
          throw new Error(result.message || "ไม่สามารถลบข้อมูลได้");
        }
        contacts.value = contacts.value.filter((item) => item.contact_id !== contact.contact_id);
        success.value = result.message;
      } catch (err) {
        error.value = err.message;
      } finally {
        deletingId.value = null;
      }
    };

    onMounted(() => {
      contactModal = new window.bootstrap.Modal(modalElement.value);
      fetchContacts();
    });

    onBeforeUnmount(() => contactModal?.dispose());

    return {
      contacts,
      loading,
      saving,
      deletingId,
      error,
      success,
      editContact,
      isEditMode,
      modalElement,
      openAddModal,
      openEditModal,
      closeModal,
      saveContact,
      deleteContact
    };
  }
};
</script>

<style scoped>
.contact-detail {
  min-width: 220px;
  max-width: 420px;
  white-space: pre-wrap;
  overflow-wrap: anywhere;
}
</style>

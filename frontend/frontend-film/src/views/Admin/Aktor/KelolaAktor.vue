<template>
  <div class="container">
    <div class="page-title">
      <RouterLink to="/dashboard" class="btn-back">← Dashboard</RouterLink>
      <div class="title-row">
        <h1>🌟 Kelola Aktor</h1>
        <RouterLink to="/tambah-aktor" class="btn btn-primary">➕ Tambah Aktor</RouterLink>
      </div>
    </div>

    <div v-if="successMsg" class="alert alert-success">✅ {{ successMsg }}</div>
    <p v-if="loading" class="loading-text">⏳ Memuat data aktor...</p>

    <div v-else class="table-wrapper">
      <table class="film-table">
        <thead>
          <tr><th>No</th><th>Nama Aktor</th><th>Gender</th><th>Tgl Lahir</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <tr v-if="aktors.length === 0">
            <td colspan="5" class="empty-row">Belum ada data aktor.</td>
          </tr>
          <tr v-for="(aktor, index) in aktors" :key="aktor.id">
            <td>{{ index + 1 }}</td>
            <td class="film-title-cell">{{ aktor.nama_aktor }}</td>
            <!-- Tampilkan teks lengkap, bukan kode L/P -->
            <td>{{ aktor.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td>{{ aktor.tanggal_lahir }}</td>
            <td>
              <div class="action-btns">
                <RouterLink :to="'/edit-aktor/' + aktor.id" class="btn-action btn-edit">✏️ Edit</RouterLink>
                <button @click="hapusAktor(aktor.id, aktor.nama_aktor)"
                  :disabled="deletingId === aktor.id" class="btn-action btn-delete">
                  <span v-if="deletingId === aktor.id">⏳</span>
                  <span v-else>🗑️ Hapus</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal-box">
        <h3>⚠️ Konfirmasi Hapus</h3>
        <p>Yakin menghapus aktor: <strong>{{ aktorToDelete?.nama_aktor }}</strong>?</p>
        <p class="modal-warning">Tindakan ini tidak bisa dibatalkan!</p>
        <div class="modal-actions">
          <button @click="showModal = false" class="btn-modal-cancel">Batal</button>
          <button @click="konfirmasiHapus" class="btn-modal-delete">🗑️ Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted }        from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import api                       from '../../../utils/api'

const router       = useRouter()
const aktors       = ref([])
const loading      = ref(true)
const successMsg   = ref('')
const deletingId   = ref(null)
const showModal    = ref(false)
const aktorToDelete = ref(null)

onMounted(async () => { await ambilAktor() })

const ambilAktor = async () => {
  try {
    loading.value = true
    const res = await api.get('/aktor')
    aktors.value = res.data.data
  } catch (err) { console.error(err) }
  finally { loading.value = false }
}

const hapusAktor = (id, nama_aktor) => {
  aktorToDelete.value = { id, nama_aktor }
  showModal.value     = true
}

const konfirmasiHapus = async () => {
  const id = aktorToDelete.value.id
  showModal.value = false
  try {
    deletingId.value = id
    await api.delete(`/aktor/${id}`)
    aktors.value = aktors.value.filter(a => a.id !== id)
    successMsg.value = `Aktor "${aktorToDelete.value.nama_aktor}" berhasil dihapus!`
    setTimeout(() => { successMsg.value = '' }, 3000)
  } catch (err) { alert('Gagal menghapus aktor!') }
  finally { deletingId.value = null; aktorToDelete.value = null }
}
</script>
<template>
  <div class="container">
    <div class="page-title">
      <RouterLink to="/dashboard" class="btn-back">← Dashboard</RouterLink>
      <div class="title-row">
        <h1>🌟 Kelola Aktor</h1>
        <RouterLink to="/tambah-aktor" class="btn btn-primary">➕ Tambah Aktor</RouterLink>
      </div>
    </div>

    <!-- Pesan Notifikasi -->
    <div v-if="successMsg" class="alert alert-success">✅ {{ successMsg }}</div>
    <div v-if="errorMsg" class="alert alert-danger">❌ {{ errorMsg }}</div>

    <p v-if="loading" class="loading-text">⏳ Memuat data aktor...</p>

    <div v-else class="table-wrapper">
      <table class="film-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Foto</th> <!-- Tambahan Kolom Foto -->
            <th>Nama Aktor</th>
            <th>Jenis Kelamin</th>
            <th>Tgl Lahir</th>
            <th>Umur</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="aktors.length === 0">
            <!-- Ubah colspan menjadi 7 karena ada tambahan kolom foto -->
            <td colspan="7" class="empty-row">Belum ada data aktor.</td>
          </tr>
          <tr v-for="(aktor, index) in aktors" :key="aktor.id">
            <td>{{ index + 1 }}</td>
            
            <!-- Menampilkan Foto -->
            <td>
              <img 
                v-if="aktor.foto" 
                :src="getFotoUrl(aktor.foto)" 
                alt="Foto Aktor" 
                class="aktor-foto" 
              />
              <div v-else class="no-foto">❌ No Photo</div>
            </td>
            
            <td class="film-title-cell">{{ aktor.nama_aktor }}</td>
            <td>{{ aktor.jenis_kelamin === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td>{{ formatTanggal(aktor.tanggal_lahir) }}</td>
            <td>{{ hitungUmur(aktor.tanggal_lahir) }}</td>
            <td>
              <div class="action-btns">
                <RouterLink :to="'/edit-aktor/' + aktor.id" class="btn-action btn-edit">✏️ Edit</RouterLink>
                <button @click="hapusAktor(aktor.id, aktor.nama_aktor)" class="btn-action btn-delete">
                  🗑️ Hapus
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div v-if="showModal" class="modal-overlay" @click.self="tutupModal">
      <div class="modal-box">
        <h3>⚠️ Konfirmasi Hapus</h3>
        <p>Yakin menghapus aktor: <strong>{{ aktorToDelete?.nama_aktor }}</strong>?</p>
        <p class="modal-warning">Tindakan ini tidak bisa dibatalkan!</p>
        <div class="modal-actions">
          <button @click="tutupModal" class="btn-modal-cancel" :disabled="isDeleting">Batal</button>
          <button @click="konfirmasiHapus" class="btn-modal-delete" :disabled="isDeleting">
            <span v-if="isDeleting">⏳ Menghapus...</span>
            <span v-else>🗑️ Hapus</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink }     from 'vue-router'
import api                from '../../../utils/api'

const aktors     = ref([])
const loading    = ref(true)
const successMsg = ref('')
const errorMsg   = ref('')

const showModal     = ref(false)
const isDeleting    = ref(false)
const aktorToDelete = ref(null)

onMounted(async () => { 
  await ambilAktor() 
})

const ambilAktor = async () => {
  try {
    loading.value = true
    errorMsg.value = ''
    const res = await api.get('/aktor')
    aktors.value = res.data.data
  } catch (err) { 
    console.error(err)
    errorMsg.value = 'Gagal memuat data aktor dari server.'
  } finally { 
    loading.value = false 
  }
}

// Fungsi untuk mendapatkan URL foto (Sesuaikan dengan setup backend Anda)
const getFotoUrl = (foto) => {
  if (!foto) return ''
  // Jika API sudah mengembalikan link lengkap (http://...)
  if (foto.startsWith('http')) {
    return foto
  }
  // JIKA backend HANYA mengembalikan nama file (contoh: "aktor1.jpg"), 
  // hapus tanda komentar di bawah dan sesuaikan URL backend (misal Laravel: http://localhost:8000/storage/)
  // return `http://localhost:8000/storage/aktor/${foto}`
  
  return foto
}

// Fungsi format tanggal
const formatTanggal = (tanggal) => {
  if (!tanggal) return '-'
  return new Intl.DateTimeFormat('id-ID', { 
    day: '2-digit', month: 'long', year: 'numeric' 
  }).format(new Date(tanggal))
}

// Fungsi otomatis hitung umur berdasarkan tanggal lahir
const hitungUmur = (tanggal_lahir) => {
  if (!tanggal_lahir) return '-'
  
  const today = new Date()
  const birthDate = new Date(tanggal_lahir)
  
  let age = today.getFullYear() - birthDate.getFullYear()
  const monthDiff = today.getMonth() - birthDate.getMonth()
  
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--
  }
  
  return `${age} Tahun`
}

const hapusAktor = (id, nama_aktor) => {
  aktorToDelete.value = { id, nama_aktor }
  showModal.value     = true
}

const tutupModal = () => {
  if (isDeleting.value) return 
  showModal.value     = false
  aktorToDelete.value = null
}

const konfirmasiHapus = async () => {
  if (!aktorToDelete.value) return
  
  const id = aktorToDelete.value.id
  isDeleting.value = true
  
  try {
    await api.delete(`/aktor/${id}`)
    aktors.value = aktors.value.filter(a => a.id !== id)
    
    successMsg.value = `Aktor "${aktorToDelete.value.nama_aktor}" berhasil dihapus!`
    setTimeout(() => { successMsg.value = '' }, 3000)
    
    tutupModal()
  } catch (err) { 
    console.error(err)
    alert('Gagal menghapus aktor! Silakan coba lagi.') 
  } finally { 
    isDeleting.value = false 
  }
}
</script>

<style scoped>
/* Container & Header */
.container { padding-bottom: 40px; }
.page-title { margin-bottom: 24px; }
.title-row { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; }
.page-title h1 { font-size: 28px; color: #1a1a2e; margin: 0; }
.btn-back { color: #666; font-size: 14px; text-decoration: none; }
.btn-back:hover { color: #e94560; }

/* Buttons */
.btn { padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: opacity 0.2s; }
.btn-primary { background: #e94560; color: white; }
.btn-primary:hover { opacity: 0.9; }

/* Alerts & Status */
.alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.alert-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.loading-text { color: #666; font-size: 14px; font-weight: 500; }

/* Table Wrapper */
.table-wrapper {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  overflow: hidden;
}

.film-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 14px;
}

.film-table th {
  background: #f8fafc;
  padding: 16px;
  color: #333;
  font-weight: 600;
  border-bottom: 2px solid #e2e8f0;
}

.film-table td {
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  color: #475569;
  vertical-align: middle;
}

.film-table tr:hover { background: #f8fafc; }
.film-title-cell { font-weight: 600; color: #1a1a2e; }
.empty-row { text-align: center; color: #94a3b8; font-style: italic; padding: 32px !important; }

/* --- CSS UNTUK FOTO --- */
.aktor-foto {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.no-foto {
  font-size: 12px;
  color: #94a3b8;
  font-style: italic;
  background: #f1f5f9;
  padding: 8px;
  border-radius: 8px;
  text-align: center;
  width: max-content;
}
/* ---------------------- */

/* Action Buttons in Table */
.action-btns { display: flex; gap: 8px; }
.btn-action { 
  padding: 8px 12px; 
  border-radius: 8px; 
  font-size: 13px; 
  font-weight: 600; 
  text-decoration: none; 
  border: none; 
  cursor: pointer;
  transition: background 0.2s; 
}
.btn-edit { background: #fef3c7; color: #d97706; }
.btn-edit:hover { background: #fde68a; }
.btn-delete { background: #fee2e2; color: #dc2626; }
.btn-delete:hover:not(:disabled) { background: #fca5a5; }
.btn-delete:disabled { opacity: 0.6; cursor: not-allowed; }

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
  backdrop-filter: blur(4px);
}

.modal-box {
  background: white;
  padding: 24px;
  border-radius: 16px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.modal-box h3 { margin: 0 0 12px 0; color: #1a1a2e; font-size: 20px; }
.modal-box p { color: #475569; font-size: 14px; margin-bottom: 8px; }
.modal-warning { color: #dc2626 !important; font-weight: 600; font-size: 13px !important; margin-bottom: 24px !important; }

.modal-actions { display: flex; justify-content: flex-end; gap: 12px; }
.btn-modal-cancel { 
  background: #f1f5f9; 
  color: #475569; 
  padding: 10px 16px; 
  border-radius: 8px; 
  border: none; 
  font-weight: 600; 
  cursor: pointer; 
}
.btn-modal-cancel:hover:not(:disabled) { background: #e2e8f0; }

.btn-modal-delete { 
  background: #e94560; 
  color: white; 
  padding: 10px 16px; 
  border-radius: 8px; 
  border: none; 
  font-weight: 600; 
  cursor: pointer; 
}
.btn-modal-delete:hover:not(:disabled) { background: #d13d56; }
.btn-modal-cancel:disabled, .btn-modal-delete:disabled { opacity: 0.6; cursor: not-allowed; }
</style>
<template>
  <div class="container">
    <div class="page-title">
      <RouterLink to="/dashboard" class="btn-back">← Dashboard</RouterLink>
      <div class="title-row">
        <h1>🎭 Kelola Genre</h1>
        <RouterLink to="/tambah-genre" class="btn btn-primary">➕ Tambah Genre</RouterLink>
      </div>
    </div>

    <div v-if="successMsg" class="alert alert-success">✅ {{ successMsg }}</div>
    <p v-if="loading" class="loading-text">⏳ Memuat data genre...</p>

    <div v-else class="table-wrapper">
      <table class="film-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Genre</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="genres.length === 0">
            <td colspan="3" class="empty-row">Belum ada data genre.</td>
          </tr>
          <tr v-for="(genre, index) in genres" :key="genre.id">
            <td>{{ index + 1 }}</td>
            <td class="film-title-cell">{{ genre.nama_genre }}</td>
            <td>
              <div class="action-btns">
                <RouterLink :to="'/edit-genre/' + genre.id" class="btn-action btn-edit">✏️ Edit</RouterLink>
                <button 
                  @click="hapusGenre(genre.id, genre.nama_genre)"
                  :disabled="deletingId === genre.id" 
                  class="btn-action btn-delete"
                >
                  <span v-if="deletingId === genre.id">⏳</span>
                  <span v-else>🗑️ Hapus</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Hapus -->
    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal-box">
        <h3>⚠️ Konfirmasi Hapus</h3>
        <p>Yakin menghapus genre: <strong>{{ genreToDelete?.nama_genre }}</strong>?</p>
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

const router        = useRouter()
const genres        = ref([])
const loading       = ref(true)
const successMsg    = ref('')
const deletingId    = ref(null)
const showModal     = ref(false)
const genreToDelete = ref(null)

onMounted(async () => { await ambilGenre() })

const ambilGenre = async () => {
  try {
    loading.value = true
    const res = await api.get('/genre')
    genres.value = res.data.data
  } catch (err) { console.error(err) }
  finally { loading.value = false }
}

const hapusGenre = (id, nama_genre) => {
  genreToDelete.value = { id, nama_genre }
  showModal.value     = true
}

const konfirmasiHapus = async () => {
  const id = genreToDelete.value.id
  showModal.value = false
  try {
    deletingId.value = id
    await api.delete(`/genre/${id}`)
    genres.value = genres.value.filter(g => g.id !== id)
    successMsg.value = `Genre "${genreToDelete.value.nama_genre}" berhasil dihapus!`
    setTimeout(() => { successMsg.value = '' }, 3000)
  } catch (err) {
    alert('Gagal menghapus genre!')
  } finally {
    deletingId.value = null
    genreToDelete.value = null
  }
}
</script>

<style scoped>
.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 24px 16px;
}

.page-title { 
  margin-bottom: 24px; 
}

.btn-back { 
  color: #666; 
  font-size: 14px; 
  text-decoration: none;
}

.btn-back:hover { 
  color: #e94560; 
  text-decoration: none; 
}

.title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
}

.title-row h1 { 
  font-size: 28px; 
  color: #1a1a2e; 
  margin: 0;
}

/* Button Primary sesuai tema project */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 14px;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #e94560;
  color: white;
}

.btn-primary:hover {
  background: #d63852;
  box-shadow: 0 4px 12px rgba(233, 69, 96, 0.3);
}

/* Alert & Loading */
.alert-success {
  background: #d1fae5;
  color: #065f46;
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-size: 14px;
  font-weight: 500;
}

.loading-text {
  text-align: center;
  color: #666;
  font-size: 14px;
  padding: 30px 0;
}

/* Table Design */
.table-wrapper {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.film-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.film-table th {
  background: #f8f9fa;
  color: #333;
  font-weight: 600;
  font-size: 13px;
  padding: 14px 20px;
  border-bottom: 2px solid #e0e0e0;
}

.film-table th:first-child { width: 60px; text-align: center; }
.film-table th:last-child { width: 160px; text-align: center; }

.film-table td {
  padding: 14px 20px;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
  color: #333;
  vertical-align: middle;
}

.film-table td:first-child { text-align: center; color: #888; }
.film-table tbody tr:hover { background: #fafafa; }

.film-title-cell {
  font-weight: 600;
  color: #1a1a2e;
}

.empty-row {
  text-align: center;
  color: #999;
  padding: 30px !important;
}

/* Action Buttons */
.action-btns {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.btn-action {
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-edit {
  background: #f0f0f0;
  color: #555;
}

.btn-edit:hover {
  background: #e0e0e0;
}

.btn-delete {
  background: #fee2e2;
  color: #e94560;
}

.btn-delete:hover:not(:disabled) {
  background: #fca5a5;
  color: #fff;
}

.btn-delete:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Modal Overlay & Card */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal-box {
  background: white;
  border-radius: 16px;
  padding: 24px 28px;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.modal-box h3 {
  margin-top: 0;
  margin-bottom: 12px;
  font-size: 18px;
  color: #1a1a2e;
}

.modal-box p {
  margin: 4px 0;
  font-size: 14px;
  color: #555;
}

.modal-warning {
  color: #e94560 !important;
  font-size: 12px !important;
  font-weight: 600;
  margin-top: 6px !important;
}

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 20px;
}

.btn-modal-cancel {
  background: #f0f0f0;
  color: #555;
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-modal-cancel:hover { background: #e0e0e0; }

.btn-modal-delete {
  background: #e94560;
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-modal-delete:hover { background: #d63852; }
</style>
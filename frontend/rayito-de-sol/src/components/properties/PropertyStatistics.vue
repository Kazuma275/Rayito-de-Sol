<template>
  <div class="statistics-container">
    <h2 class="section-title">Estadísticas de tus propiedades</h2>
    
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <HomeIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ statistics?.totalProperties ?? '-' }}</p>
          <h3 class="stat-title">Propiedades</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <CalendarIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ statistics?.totalBookings ?? '-' }}</p>
          <h3 class="stat-title">Reservas</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <TrendingUpIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ statistics?.occupancyRate ?? '-' }}%</p>
          <h3 class="stat-title">Ocupación</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <EuroIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ statistics?.totalRevenue ?? '-' }}€</p>
          <h3 class="stat-title">Ingresos</h3>
        </div>
      </div>
    </div>
    
    <div class="charts-container">
      <div class="chart-card">
        <h3 class="chart-title">Reservas por mes</h3>
        <div class="chart-placeholder">
          <div class="chart-bars">
            <div 
              v-for="(value, i) in months"
              :key="i"
              class="chart-bar"
              :style="{ height: `${maxBookings ? ((statistics?.bookingsByMonth?.[i+1] || 0) / maxBookings) * 100 : 0}%` }"
            >
              <span class="bar-value">{{ statistics?.bookingsByMonth?.[i+1] || 0 }}</span>
            </div>
          </div>
          <div class="chart-labels">
            <span v-for="month in months" :key="month">{{ month }}</span>
          </div>
        </div>
      </div>
      
      <div class="chart-card">
        <h3 class="chart-title">Ingresos por propiedad</h3>
        <div class="chart-placeholder">
          <div class="property-revenue" v-for="property in statistics?.revenueByProperty || []" :key="property.id">
            <div class="property-info">
              <span class="property-name">{{ property.name }}</span>
              <span class="property-value">{{ property.revenue }}€</span>
            </div>
            <div class="revenue-bar-container">
              <div 
                class="revenue-bar" 
                :style="{ width: `${maxRevenue ? (property.revenue / maxRevenue) * 100 : 0}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="loading" style="margin-top:2rem;text-align:center">Cargando estadísticas...</div>
    <div v-if="error" style="color:red; margin-top:2rem;text-align:center">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { HomeIcon, CalendarIcon, TrendingUpIcon, EuroIcon } from 'lucide-vue-next'

const statistics = ref(null)
const loading = ref(false)
const error = ref(null)

const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']

const maxBookings = computed(() => {
  if (!statistics.value?.bookingsByMonth) return 0
  return Math.max(...Object.values(statistics.value.bookingsByMonth))
})

const maxRevenue = computed(() => {
  if (!statistics.value?.revenueByProperty?.length) return 0
  return Math.max(...statistics.value.revenueByProperty.map(p => p.revenue))
})

const fetchStatistics = async () => {
  loading.value = true
  error.value = null
  try {
    const token = localStorage.getItem('auth_token')
    const res = await axios.get('http://localhost:8000/api/statistics', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    statistics.value = res.data
  } catch (err) {
    error.value = err.response?.data?.message || err.message
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchStatistics()
})
</script>

<style scoped>
.statistics-container {
  margin-bottom: 3rem;
}

.section-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0 0 2rem;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.stat-card {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 53, 128, 0.1);
}

.stat-icon-wrapper {
  position: relative;
  width: 60px;
  height: 60px;
  border-radius: 12px;
  background: linear-gradient(135deg, #e6f0ff 0%, #cce0ff 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon {
  width: 30px;
  height: 30px;
  color: #0071c2;
  position: relative;
  z-index: 1;
}

.stat-icon-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle, rgba(0, 113, 194, 0.2) 0%, rgba(0, 113, 194, 0) 70%);
  border-radius: 12px;
  opacity: 0;
  transition: opacity 0.3s, transform 0.3s;
}

.stat-card:hover .stat-icon-glow {
  opacity: 1;
  transform: scale(1.2);
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #003580;
  margin: 0 0 0.25rem;
}

.stat-title {
  font-size: 1rem;
  color: #64748b;
  margin: 0;
}

.charts-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1.5rem;
}

.chart-card {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.chart-title {
  font-size: 1.2rem;
  color: #003580;
  margin: 0 0 1.5rem;
}

.chart-placeholder {
  height: 300px;
  display: flex;
  flex-direction: column;
}

.chart-bars {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  height: 250px;
  margin-bottom: 1rem;
}

.chart-bar {
  width: 24px;
  background: linear-gradient(to top, #0071c2, #003580);
  border-radius: 4px 4px 0 0;
  position: relative;
  transition: height 0.5s ease;
}

.bar-value {
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.8rem;
  font-weight: 600;
  color: #003580;
}

.chart-labels {
  display: flex;
  justify-content: space-between;
}

.chart-labels span {
  font-size: 0.8rem;
  color: #64748b;
  text-align: center;
  width: 24px;
}

.property-revenue {
  margin-bottom: 1.5rem;
}

.property-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.property-name {
  font-weight: 500;
  color: #1e293b;
}

.property-value {
  font-weight: 600;
  color: #003580;
}

.revenue-bar-container {
  height: 8px;
  background-color: #e6f0ff;
  border-radius: 4px;
  overflow: hidden;
}

.revenue-bar {
  height: 100%;
  background: linear-gradient(to right, #0071c2, #003580);
  border-radius: 4px;
  transition: width 0.5s ease;
}

@media (max-width: 992px) {
  .charts-container {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
  
  .stat-card {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .chart-bars {
    height: 200px;
  }
}

@media (max-width: 500px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .chart-placeholder {
    height: 250px;
  }
  
  .chart-bar {
    width: 20px;
  }
  
  .chart-labels span {
    width: 20px;
    font-size: 0.7rem;
  }
}
</style>
<template>
  <div class="settings-panel">
    <h3 class="panel-title">Términos y Condiciones</h3>
    
    <div class="terms-container">
      <div class="terms-section">
        <h4>Términos de Servicio</h4>
        <p>
          Al utilizar nuestra plataforma, usted acepta cumplir con estos términos y condiciones. 
          Rayito de Sol proporciona una plataforma para que los propietarios gestionen sus propiedades 
          y reservas. Nos reservamos el derecho de modificar estos términos en cualquier momento.
        </p>
      </div>
      
      <div class="terms-section">
        <h4>Política de Cancelación</h4>
        <div class="cancellation-policy">
          <div class="policy-item">
            <h5>Flexible</h5>
            <p>Reembolso completo si se cancela 24 horas antes de la llegada.</p>
            <label class="radio-container">
              <input type="radio" name="cancellation-policy" value="flexible" v-model="selectedPolicy" />
              <span class="radio-checkmark"></span>
            </label>
          </div>
          
          <div class="policy-item">
            <h5>Moderada</h5>
            <p>Reembolso del 50% si se cancela 5 días antes de la llegada.</p>
            <label class="radio-container">
              <input type="radio" name="cancellation-policy" value="moderate" v-model="selectedPolicy" />
              <span class="radio-checkmark"></span>
            </label>
          </div>
          
          <div class="policy-item">
            <h5>Estricta</h5>
            <p>Reembolso del 50% si se cancela 7 días antes de la llegada.</p>
            <label class="radio-container">
              <input type="radio" name="cancellation-policy" value="strict" v-model="selectedPolicy" />
              <span class="radio-checkmark"></span>
            </label>
          </div>
        </div>
      </div>
      
      <div class="terms-section">
        <h4>Normas de la Casa</h4>
        <div class="house-rules">
          <div v-for="(rule, index) in houseRules" :key="index" class="rule-item">
            <label class="checkbox-container">
              <input type="checkbox" v-model="rule.enabled" />
              <span class="checkbox-checkmark"></span>
              {{ rule.text }}
            </label>
          </div>
          
          <div class="add-rule">
            <input 
              type="text" 
              v-model="newRule" 
              placeholder="Añadir nueva norma..." 
              class="form-input"
              @keyup.enter="addHouseRule"
            />
            <button class="btn btn-outline" @click="addHouseRule">Añadir</button>
          </div>
        </div>
      </div>
      
      <div class="terms-section">
        <h4>Política de Privacidad</h4>
        <p>
          Rayito de Sol respeta su privacidad y se compromete a proteger sus datos personales. 
          Recopilamos información para mejorar nuestros servicios y proporcionar una mejor experiencia. 
          No compartimos sus datos con terceros sin su consentimiento explícito.
        </p>
        <div class="privacy-options">
          <label class="checkbox-container">
            <input type="checkbox" v-model="privacyOptions.marketing" />
            <span class="checkbox-checkmark"></span>
            Recibir comunicaciones de marketing
          </label>
          
          <label class="checkbox-container">
            <input type="checkbox" v-model="privacyOptions.thirdParty" />
            <span class="checkbox-checkmark"></span>
            Permitir compartir datos con socios de confianza
          </label>
        </div>
      </div>
      
      <div class="form-actions">
        <button class="save-button">Guardar cambios</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const selectedPolicy = ref('moderate');

const houseRules = ref([
  { text: 'No se permiten mascotas', enabled: true },
  { text: 'No se permite fumar', enabled: true },
  { text: 'No se permiten fiestas', enabled: true },
  { text: 'Hora de llegada: 14:00 - 20:00', enabled: true },
  { text: 'Hora de salida: 12:00', enabled: true }
]);

const newRule = ref('');

const addHouseRule = () => {
  if (newRule.value.trim() === '') return;
  
  houseRules.value.push({
    text: newRule.value.trim(),
    enabled: true
  });
  
  newRule.value = '';
};

const privacyOptions = ref({
  marketing: true,
  thirdParty: false
});
</script>

<style scoped>
.settings-panel {
  max-width: 700px;
}

.panel-title {
  font-size: 1.4rem;
  color: #003580;
  margin: 0 0 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e6f0ff;
}

.terms-container {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.terms-section {
  border-bottom: 1px solid #e6f0ff;
  padding-bottom: 2rem;
}

.terms-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.terms-section h4 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0 0 1rem;
}

.terms-section p {
  margin: 0 0 1rem;
  line-height: 1.6;
  color: #1e293b;
}

.cancellation-policy {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.policy-item {
  display: flex;
  align-items: flex-start;
  padding: 1rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  position: relative;
  background-color: #f8fafc;
}

.policy-item h5 {
  font-size: 1.1rem;
  margin: 0 0 0.5rem;
  color: #1e293b;
}

.policy-item p {
  font-size: 0.95rem;
  margin: 0;
  flex: 1;
  color: #64748b;
}

.radio-container {
  position: relative;
  padding-left: 25px;
  cursor: pointer;
  user-select: none;
  margin-left: 1rem;
  display: flex;
  align-items: center;
}

.radio-container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.radio-checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 18px;
  width: 18px;
  background-color: #f1f5f9;
  border: 1px solid #cce0ff;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.radio-container:hover input ~ .radio-checkmark {
  background-color: #e6f0ff;
}

.radio-container input:checked ~ .radio-checkmark {
  background-color: #0071c2;
  border-color: #0071c2;
}

.radio-checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.radio-container input:checked ~ .radio-checkmark:after {
  display: block;
}

.radio-container .radio-checkmark:after {
  top: 5px;
  left: 5px;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: white;
}

.house-rules {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.rule-item {
  display: flex;
  align-items: center;
}

.checkbox-container {
  position: relative;
  padding-left: 30px;
  cursor: pointer;
  user-select: none;
  display: flex;
  align-items: center;
  color: #1e293b;
}

.checkbox-container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.checkbox-checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 20px;
  width: 20px;
  background-color: #f1f5f9;
  border: 1px solid #cce0ff;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.checkbox-container:hover input ~ .checkbox-checkmark {
  background-color: #e6f0ff;
}

.checkbox-container input:checked ~ .checkbox-checkmark {
  background-color: #0071c2;
  border-color: #0071c2;
}

.checkbox-checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.checkbox-container input:checked ~ .checkbox-checkmark:after {
  display: block;
}

.checkbox-container .checkbox-checkmark:after {
  left: 7px;
  top: 3px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.add-rule {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}

.add-rule .form-input {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.add-rule .form-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.add-rule .btn {
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.add-rule .btn-outline {
  background-color: transparent;
  border: 1px solid #0071c2;
  color: #0071c2;
}

.add-rule .btn-outline:hover {
  background-color: #0071c2;
  color: white;
}

.privacy-options {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 1rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 2rem;
}

.save-button {
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.save-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

@media (max-width: 576px) {
  .add-rule {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .form-actions {
    justify-content: center;
  }
  
  .save-button {
    width: 100%;
  }
}
</style>
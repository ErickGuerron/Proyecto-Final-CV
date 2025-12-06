// contact-script.js

// ========================================
// CARGA AUTOMÁTICA DE AVATARES Y USUARIOS
// ========================================

async function loadGitHubData(devs) {
  const updated = [];

  for (const dev of devs) {
    if (!dev.githubUsername) {
      updated.push(dev);
      continue;
    }

    try {
      const response = await fetch(`https://api.github.com/users/${dev.githubUsername}`);
      const data = await response.json();

      updated.push({
        ...dev,
        githubAvatar: data.avatar_url || dev.githubAvatar,
        githubUsername: data.login || dev.githubUsername
      });

    } catch (error) {
      console.error(`Error cargando GitHub de ${dev.nombre}:`, error);
      updated.push(dev); 
    }
  }

  return updated;
}

// ========================================
// LISTA DE DESARROLLADORES COMPLETA
// ========================================

let developers = [
  {
    id: 1,
    nombre: "Anthony Punina Chisag",
    githubUsername: "AnthonyPSW",
    githubAvatar: "",
    areaDevelopment: "Full-Stack (.NET Core, NestJS, React, Blazor)",
    areaInteres: "Arquitectura de software, Seguridad, BD, APIs, DevOps, UX",
    ultimoProyecto: "Sistema de Facturación Electrónica con .NET Core y MyCSW.",
    descripcion: "Proactivo, disciplinado y apasionado por construir sistemas completos con buena arquitectura y UX.",
    fechaNacimiento: "21/03/2006",
    colorFavorito: "#000000",
    hobbies: ["Escuchar música", "Fútbol", "Videojuegos"],
    email: "anthonypunina06@outlook.com"
  },
  {
    id: 2,
    nombre: "Xabier David Pérez Pérez",
    githubUsername: "XabierP2006",
    githubAvatar: "",
    areaDevelopment: "Frontend (React, Tailwind, Framer Motion, dashboards)",
    areaInteres: "UI/UX, arquitectura frontend, APIs, backend NestJS, BD SQL",
    ultimoProyecto: "Sistema MyCSW con React + dashboards.",
    descripcion: "Creativo, disciplinado y detallista; enfocado en vistas limpias y buena UX.",
    fechaNacimiento: "10/12/2005",
    colorFavorito: "#00CFFF",
    hobbies: ["Escuchar música", "Fútbol", "Salir con amigos"],
    email: "idk.chavy@gmail.com"
  },
  {
    id: 3,
    nombre: "Bryan Lenin Quitto Navarrete",
    githubUsername: "Bryan-Quitto",
    githubAvatar: "",
    areaDevelopment: "Full-Stack",
    areaInteres: "Ciencia de datos / IA",
    ultimoProyecto: "Aseguradora Savalta",
    descripcion: "Apasionado por crear aplicaciones modernas e integrar IA.",
    fechaNacimiento: "11/04/2003",
    colorFavorito: "#FF0000",
    hobbies: ["Anime", "Videojuegos"],
    email: "bryanleninqn@gmail.com"
  },
  {
    id: 4,
    nombre: "Maia Rojas",
    githubUsername: "May360-ai",
    githubAvatar: "",
    areaDevelopment: "QA",
    areaInteres: "Bases de datos",
    ultimoProyecto: "Página de Facturación Aether",
    descripcion: "Muy sensible, pero responsable cuando es necesario.",
    fechaNacimiento: "11/12/2005",
    colorFavorito: "#8000FF",
    hobbies: ["Dibujar"],
    email: "maiacrhec@gmail.com"
  },
  {
    id: 5,
    nombre: "Jimmy Alexander Añilema Hoffmann",
    githubUsername: "AHJimmy06",
    githubAvatar: "",
    areaDevelopment: "Full-Stack",
    areaInteres: "Ciencia de datos (IA)",
    ultimoProyecto: "Aseguradora Savalta",
    descripcion: "Apasionado por la ciencia de datos y el desarrollo para mejorar la vida de las personas.",
    fechaNacimiento: "09/03/2025",
    colorFavorito: "#0000FF",
    hobbies: ["Videojuegos"],
    email: "jimmy.anilema234@gmail.co"
  },
  {
    id: 6,
    nombre: "Erick Guerrón",
    githubUsername: "ErickGuerron",
    githubAvatar: "",
    areaDevelopment: "Full-Stack (backend empresarial)",
    areaInteres: "Arquitectura, sistemas distribuidos, DevOps básico",
    ultimoProyecto: "Sistema de Facturación Electrónica + MyCSW",
    descripcion: "Orientado al detalle, amante de la arquitectura limpia y la calidad del software.",
    fechaNacimiento: "No especificado",
    colorFavorito: "#000000",
    hobbies: [
      "Aprender tecnologías",
      "Diseñar arquitecturas",
      "Investigar buenas prácticas"
    ],
    email: ""
  },
  {
    id: 7,
    nombre: "Maria Belen Zapata Reyes",
    githubUsername: "Mabe-Zapata",
    githubAvatar: "",
    areaDevelopment: "Full-Stack / IA & IoT",
    areaInteres: "Ciencia de Datos, IA, IoT",
    ultimoProyecto: "Sistema de facturación electrónica con arquitectura Onion",
    descripcion: "Perfil técnico + creativo, con pasión por IA, IoT y desarrollo full-stack con propósito.",
    fechaNacimiento: "15/02/2005",
    colorFavorito: "#fab82bff",
    hobbies: ["Baile", "K-pop", "Series de acción", "Cultura coreana y tailandesa", "Lectura"],
    email: "belenzapatareyes@gmail.com"
  },
  {
    id: 8,
    nombre: "Washington Esteban Villalba Lopez",
    githubUsername: "Esteban-Vlz",
    githubAvatar: "",
    areaDevelopment: "Frontend (interfaces modernas y usabilidad)",
    areaInteres: "IA + Ciencia de datos aplicadas a la web",
    ultimoProyecto: "Cursos_Universidad",
    descripcion: "Frontend lover, gamer, amante del anime y el dibujo. Fan de crear interfaces limpias y usables.",
    fechaNacimiento: "24/07/2004",
    colorFavorito: "#000000",
    hobbies: ["Anime", "Música", "Videojuegos", "Dibujar"],
    email: "villalbaesteban007@gmail.com"
  },
  {
    id: 9,
    nombre: "Patricio Tisalema",
    githubUsername: "NoMerlyn",
    githubAvatar: "",
    areaDevelopment: "Full-Stack / Proyectos personales",
    areaInteres: "Aprendizaje autodidacta, pruebas y experimentación",
    ultimoProyecto: "Repositorios personales activos en GitHub",
    descripcion: "Desarrollador explorador del ecosistema open-source y de proyectos experimentales.",
    fechaNacimiento: "No especificado",
    colorFavorito: "#444444",
    hobbies: ["Experimentar código", "Proyectos personales"],
    email: ""
  }
];

// ========================================
// RENDER DE TARJETAS
// ========================================

function generateDeveloperCards() {
  const container = document.getElementById('teamCardsContainer');
  container.innerHTML = "";

  developers.forEach(dev => {
    const card = document.createElement('div');
    card.className = 'col-lg-4 col-md-6';
    card.innerHTML = `
      <div class="developer-card" style="--dev-color: ${dev.colorFavorito}" onclick="openModal(${dev.id})">
          <div class="developer-avatar">
              <img src="${dev.githubAvatar}" alt="${dev.nombre}">
              <div class="color-indicator" style="background: ${dev.colorFavorito};"></div>
          </div>
          <div class="developer-info">
              <h3 class="developer-name">${dev.nombre.split(' ')[0]} ${dev.nombre.split(' ')[1]}</h3>
              <p class="developer-role">${dev.areaDevelopment}</p>
              <a href="https://github.com/${dev.githubUsername}" class="github-link" target="_blank" onclick="event.stopPropagation()">
                  <i class="fab fa-github"></i> @${dev.githubUsername}
              </a>
          </div>
      </div>
    `;
    container.appendChild(card);
  });
}

// ========================================
// MODAL
// ========================================

function openModal(developerId) {
  const dev = developers.find(d => d.id === developerId);
  if (!dev) return;

  const modalContent = document.getElementById('modalContent');

  modalContent.innerHTML = `
    <div class="modal-header-custom" style="background: linear-gradient(135deg, ${dev.colorFavorito}, ${dev.colorFavorito}CC);">
      <img src="${dev.githubAvatar}" class="modal-avatar-large">
      <h2>${dev.nombre}</h2>
      <p>${dev.areaDevelopment}</p>
    </div>

    <div class="modal-body-custom">
      <div class="info-grid">
        <div class="info-item" style="border-left-color:${dev.colorFavorito}">
          <div class="info-label">Área de interés</div>
          <div class="info-value">${dev.areaInteres}</div>
        </div>
        <div class="info-item" style="border-left-color:${dev.colorFavorito}">
          <div class="info-label">Nacimiento</div>
          <div class="info-value">${dev.fechaNacimiento}</div>
        </div>
      </div>

      <div class="info-section">
        <div class="section-title" style="color:${dev.colorFavorito}">
          <i class="fas fa-code"></i> Último Proyecto
        </div>
        <div class="section-content">${dev.ultimoProyecto}</div>
      </div>

      <div class="info-section">
        <div class="section-title" style="color:${dev.colorFavorito}">
          <i class="fas fa-user"></i> Sobre mí
        </div>
        <div class="description-box" style="border-left-color:${dev.colorFavorito}">
          ${dev.descripcion}
        </div>
      </div>

      <div class="info-section">
        <div class="section-title" style="color:${dev.colorFavorito}">
          <i class="fas fa-heart"></i> Hobbies
        </div>
        <div class="hobbies-container">
          ${dev.hobbies.map(h => `
            <span class="hobby-tag" style="background:${dev.colorFavorito}AA">${h}</span>
          `).join("")}
        </div>
      </div>

      <div class="info-section">
        <a class="contact-email" href="mailto:${dev.email}" style="background:${dev.colorFavorito}CC">
          <i class="fas fa-envelope"></i> ${dev.email}
        </a>
      </div>
    </div>
  `;

  new bootstrap.Modal(document.getElementById('developerModal')).show();
}

// ========================================
// INICIALIZACIÓN FINAL
// ========================================

document.addEventListener("DOMContentLoaded", async () => {
  developers = await loadGitHubData(developers);
  generateDeveloperCards();
});

// Exponer modal global
window.openModal = openModal;

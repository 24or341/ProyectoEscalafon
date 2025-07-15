document.addEventListener('DOMContentLoaded', () => {
    // Verificar si hay token almacenado
    const token = localStorage.getItem('jwt_token');
    
    if (token) {
        // Validar token con backend
        AuthService.verifyToken(token)
            .then(() => loadView('dashboard'))
            .catch(() => loadView('login'));
    } else {
        loadView('login');
    }
    
    // Manejador de cambio de vistas
    document.addEventListener('click', (e) => {
        if (e.target.matches('[data-view]')) {
            e.preventDefault();
            const viewName = e.target.getAttribute('data-view');
            loadView(viewName);
        }
    });
});

function loadView(viewName) {
    fetch(`views/${viewName}.html`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('app').innerHTML = html;
            
            // Inicializar vista específica
            switch(viewName) {
                case 'login':
                    initLoginView();
                    break;
                case 'register':
                    initRegisterView();
                    break;
                case 'dashboard':
                    initDashboardView();
                    break;
                case 'empleado':
                    initEmpleadoView();
                    break;
            }
        })
        .catch(error => {
            console.error('Error loading view:', error);
            document.getElementById('app').innerHTML = '<h1>Error cargando la vista</h1>';
        });
}

// Inicializadores de vistas
function initLoginView() {
    document.getElementById('login-form').addEventListener('submit', handleLogin);
    document.getElementById('register-link').addEventListener('click', (e) => {
        e.preventDefault();
        loadView('register');
    });
}

function initRegisterView() {
    document.getElementById('register-form').addEventListener('submit', handleRegister);
    document.getElementById('login-link').addEventListener('click', (e) => {
        e.preventDefault();
        loadView('login');
    });
}

function initDashboardView() {
    // Cargar datos del usuario
    const empleado = JSON.parse(localStorage.getItem('empleado'));
    document.getElementById('user-name').textContent = `${empleado.nombres} ${empleado.apellidos}`;
    
    // Manejador de logout
    document.getElementById('logout-btn').addEventListener('click', () => {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('empleado');
        loadView('login');
    });
    
    // Cargar vista por defecto
    loadView('empleado');
}
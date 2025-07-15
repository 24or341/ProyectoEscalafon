class AuthService {
    static async handleLogin(event) {
        event.preventDefault();
        
        const dni = document.getElementById('dni').value;
        const password = document.getElementById('password').value;
        
        try {
            const response = await ApiClient.login(dni, password);
            
            // Guardar token y datos de empleado
            localStorage.setItem('jwt_token', response.token);
            localStorage.setItem('empleado', JSON.stringify(response.empleado));
            
            // Redirigir al dashboard
            loadView('dashboard');
        } catch (error) {
            showError('Error de autenticación: ' + error.message);
        }
    }
    
    static async handleRegister(event) {
        event.preventDefault();
        
        const formData = {
            dni: document.getElementById('dni').value,
            nombres: document.getElementById('nombres').value,
            apellidos: document.getElementById('apellidos').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            // Agregar más campos según el formulario
        };
        
        try {
            const response = await ApiClient.register(formData);
            
            // Auto-login después de registro
            await AuthService.handleLogin({
                preventDefault: () => {},
                target: { 
                    elements: {
                        dni: { value: formData.dni },
                        password: { value: formData.password }
                    }
                }
            });
            
        } catch (error) {
            showError('Error en registro: ' + error.message);
        }
    }
    
    static async verifyToken(token) {
        try {
            await ApiClient.verifyToken(token);
            return true;
        } catch (error) {
            return false;
        }
    }
}

// Asignar manejadores globales
window.handleLogin = AuthService.handleLogin;
window.handleRegister = AuthService.handleRegister;

function showError(message) {
    // Implementar notificación de error en la UI
    alert(message); // Temporal
}
class ApiClient {
    static async request(endpoint, method = 'GET', data = null, requiresAuth = false) {
        const url = `http://localhost:8080${endpoint}`;
        const headers = {
            'Content-Type': 'application/json'
        };
        
        if (requiresAuth) {
            const token = localStorage.getItem('jwt_token');
            if (!token) throw new Error('No autenticado');
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        const config = {
            method,
            headers,
            body: data ? JSON.stringify(data) : null
        };
        
        try {
            const response = await fetch(url, config);
            const responseData = await response.json();
            
            if (!response.ok) {
                throw new Error(responseData.error || 'Error en la solicitud');
            }
            
            return responseData;
        } catch (error) {
            console.error('API Error:', error.message);
            throw error;
        }
    }
    
    static async login(dni, password) {
        return this.request('/login', 'POST', { dni, password });
    }
    
    static async register(empleadoData) {
        return this.request('/register', 'POST', empleadoData);
    }
    
    static async getEmpleado(id) {
        return this.request(`/empleados/${id}`, 'GET', null, true);
    }
    
    static async verifyToken(token) {
        return this.request('/verify-token', 'POST', { token });
    }
}
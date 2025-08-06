import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api', // change to your backend URL
  // withCredentials: true, // needed if you use cookies (optional)
})

export default api

import axios from 'axios'
import React, { createContext, useEffect, useState } from 'react'
import Cookie from 'js-cookie'
import { get } from 'react-hook-form'
export const AppContext = createContext()

export default function AppProvider({children}) {
    const [token, setToken] = useState(localStorage.getItem('token'))
    const [user, setUser] = useState()
    // const getCSRF = () =>  axios.get('sanctum/csrf-cookie')
    const prod = 'https://api.sukikart.store/'
    const local = 'http://localhost:8000/'
    const apiClient = axios.create({
        baseURL: local,
        withCredentials: true, 
        headers: {
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
            'Content-Type': 'application/json'
        }
    })

    apiClient.interceptors.request.use(
        (config) => {
            const token = localStorage.getItem('token'); // Get token from localStorage
            if (token) {
                config.headers['Authorization'] = `Bearer ${token}`
                config.headers['X-XSRF-TOKEN'] = getCookie('XSRF-TOKEN')// Set the token dynamically
            }
            return config;
        }, (error) => {
            return Promise.reject(error);
        }
    );

    const getUser = async () => {
        try{
            const response = await apiClient.get('api/user')
            setUser(response.data)
            // console.log(response.data)
        } catch(e) {
            console.error("Error: ",e.response)
            localStorage.removeItem('token')
        } 
    } 

    useEffect(() => {
        if(token){
            getUser()
        }
    }, [token])
    return (
    <AppContext.Provider value={{ token, apiClient, user }}>
        {children}
    </AppContext.Provider>
    )
}

const getCookie = (cookieName) => {
    return Cookie.get(cookieName)
}

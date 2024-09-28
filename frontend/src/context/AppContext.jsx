import React, { createContext, useState } from 'react'

export const AppContext = createContext()

export default function AppProvider({children}) {
    const [token, setToken] = useState(localStorage.getItem('token'))
    // localStorage.setItem('token', 'abc')
    return (
    <AppContext.Provider value={{ token }}>
        {children}
    </AppContext.Provider>
    )
}

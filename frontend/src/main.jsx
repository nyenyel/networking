import React, { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import { RouterProvider } from 'react-router-dom'
import { router } from './resource/routes'
import AppProvider from './context/AppContext'

createRoot(document.getElementById('root')).render(
  // <React.StrictMode>
    <AppProvider>
      <RouterProvider router={router} />
    </AppProvider>
  // </React.StrictMode>
)

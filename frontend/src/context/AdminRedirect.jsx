import React from 'react'
import { Navigate } from 'react-router-dom'

export default function AdminRedirect() {
  return (
    <Navigate to={'/sku/admin'} replace={true}/>

  )
}

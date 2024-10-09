import React from 'react'
import { Navigate } from 'react-router-dom'

export default function UserRedirect() {
  return (
    <Navigate to={'/sku'} replace={true}/>
    
  )
}

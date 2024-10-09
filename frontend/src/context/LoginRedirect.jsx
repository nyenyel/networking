import React, { useContext } from 'react'
import { AppContext } from './AppContext'
import { Navigate } from 'react-router-dom'

export default function LoginRedirect() {


    return (
        <Navigate to={'/login'} replace={true}/>
    )

    
}

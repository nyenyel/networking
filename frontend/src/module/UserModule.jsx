import React from 'react'
import TopBar from '../component/TopBar'
import { Outlet } from 'react-router-dom'

export default function UserModule() {
    const link = ['home', 'genealogy']
    return (

    <>
        <div className=''>
            <TopBar link={link}/>
        </div>
        <div className='px-4 pt-5 overflow-hidden '> 
            <Outlet />
        </div>
    </>
    )
}

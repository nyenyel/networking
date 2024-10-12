import React from 'react'
import { Outlet } from 'react-router-dom'
import SideBar from '../component/SideBar'

export default function AdminModule() {
    const link = [
        'dashboard',
        'management',
        'member',
        'compensation',
        'profile',
        'financial',
        'transaction'
    ]
    return (
        <>
        <div className='flex gap-4'>

            <div>
                <SideBar link={link}/>
            </div>
            <div className='flex-1'>
                <Outlet />
            </div>
        </div>
        </>
    )
}

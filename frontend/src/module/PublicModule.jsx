import React from 'react'
import TopBar from '../component/TopBar'
import { Outlet } from 'react-router-dom'

export default function PublicModule() {

    const link = ['home', 'service']
    return (

    <>
        <div className='fixed top-0 left-0 right-0 z-50'>
            <TopBar link={link}/>
        </div>
        <div className=''> {/* Adjust margin-top to account for the TopBar height */}
            <Outlet />
        </div>
    </>
    )
}

import React, { useContext, useEffect, useState } from 'react'
import TopBar from '../component/TopBar'
import { Outlet } from 'react-router-dom'
import { AppContext } from '../context/AppContext'

export default function UserModule() {
    const {user} = useContext(AppContext)
    const [link, setLink] = useState(['home', 'genealogy'])
    useEffect(()=> {
        if(user){
            if(user.admin){
                setLink(['admin'])
            }
        }
    }, [user])
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

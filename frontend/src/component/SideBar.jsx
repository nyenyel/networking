import React from 'react'
import { NavLink } from 'react-router-dom'

export default function SideBar({link}) {
    return (
        <>
        <div className='flex flex-col gap-2 font-sf-extrabold text-center p-4  bg-trc text-text pb-10 px-10'>
            <div className=''>ID: ADMIN</div>
            <div className='mb-10'>ADMIN ACCOUNT</div>
            <div className='flex flex-col gap-5'>
            {link.map((item, index) => (
                <NavLink to={item} key={index} className={({ isActive }) => {
                        return `content-center text-center px-8 ${isActive && ('border-b-2  border-src text-src font-sf-extrabold')}`}}>
                    {item.toUpperCase()}
                </NavLink>
            ))}
            </div>
        </div>
        </>
    )
}

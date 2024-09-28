import React, { useContext } from 'react'
import Logo from './Logo'
import { NavLink } from 'react-router-dom'
import { AppContext } from '../context/AppContext'

export default function TopBar({link}) {
    const {token} = useContext(AppContext)
    return (
        <>
        <div className=' bg-trc flex text-white font-sf'>
            <div className='p-4 mr-6'>
                <Logo />
            </div>
            {link.map((item, index) => (
                <NavLink to={item} key={index} className={({ isActive }) => {
                        return `content-center px-8 ${isActive && ('border-b-4 border-src text-src font-sf-extrabold')}`}}>
                    {item.toUpperCase()}
                </NavLink>
            ))}
            <div className='flex-1'></div>
            {token ? (
                <div className='content-center m-4 bg-src px-10 rounded-lg hover:scale-105 cursor-pointer '>LOGOUT</div>
            ) :(
                <NavLink to={'login'} className='content-center m-4 bg-src px-10 rounded-lg'>LOGIN</NavLink>
            )}
        </div>
        </>
    )
}

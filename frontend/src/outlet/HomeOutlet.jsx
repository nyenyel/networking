import React from 'react'
import bg from '../assets/bg-1.png'
import cart from '../assets/cart.png'
import Call from '../cards/Call'
import fries from '../assets/fries.png'

export default function HomeOutlet() {
    const callForData = [
        {color: '#f5e6bf', text: 'This is the first Texts or something!', img: fries},
        {color: '#c2edb4', text: 'This is the second Texts  or something!', img: fries},
        {color: '#b7ede7', text: 'This is the third Texts or something!', img: fries},
        {color: '#b9c3ed', text: 'This is the fourth Texts  or something!', img: fries},
        {color: '#e1c1e8', text: 'This is the fifth Texts  or something!', img: fries},
        {color: '#e8bac2', text: 'This is the sixth Texts  or something!', img: fries},
        {color: '#e1c1e8', text: 'This is the sixth Texts  or something!', img: fries},
        {color: '#b9c3ed', text: 'This is the sixth Texts  or something!', img: fries},
        {color: '#b7ede7', text: 'This is the sixth Texts  or something!', img: fries},
        {color: '#f5e6bf', text: 'This is the sixth Texts  or something!', img: fries},
        {color: '#c2edb4', text: 'This is the sixth Texts  or something!', img: fries},
    ]

    return (
    <>
    <div className=" bg-home-bg bg-cover bg-center min-h-96 w-full flex select-none">
        <div className='flex-1 pl-20 pt-40 pb-28'>
            <img src={cart} alt='cart'/>
            <div className='font-pf text-white text-4xl ml-10 flex'>Ang <p className='px-3 text-src'>TINDAHAN</p> ko</div>
            <div className='font-pf text-white text-4xl ml-64 flex mt-2'>ay <p className='px-3 text-src'>TINDAHAN</p> mo</div>
        </div>
        {/* <div className='flex-none px-40'></div> */}
        <div className='flex-1 ml-40 text-white content-center'>
            <div className='flex text-4xl font-pf'>
                Get a
                <p className='ml-3 font-sf-bold text-src'>FREE GROCERY</p>
            </div>
            <div className='flex text-4xl font-pf'>
                Save up to 20%
            </div>
            <div className='bg-src h-1 w-full my-5'></div>
            <div className='flex text-2xl font-pf'>
                Get your
                <p className='mx-3 font-sf-bold text-white'>REWARD POINT</p>
                in
            </div>
            <div className='flex text-2xl font-pf'>
                your
                <p className='mx-3 font-sf-bold text-white'>SUKIKART GROCERY</p>
            </div>
            <div className='flex text-2xl font-pf'>
                <p className=' font-sf-bold text-white'>STORE</p>
            </div>
            <div className='flex'>
                <div className='font-sf-bold text-4xl bg-src px-8 py-3 rounded-lg mt-4'>JOIN NOW</div>
            </div>
        </div>
    </div>
    <div className='flex flex-wrap gap-4 px-4 py-10'>
        {callForData.map((item, index) => (
            <Call data={item} key={index}/> 
        ))}
    </div>
    </>
    )
}

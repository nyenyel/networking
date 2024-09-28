import React from 'react'
import suki from '../assets/suki.png'
import th from '../assets/transaction-history.png'
import coin from '../assets/coin.png'
import Store from '../cards/Store'

export default function UserHomeOutlet() {
  return (
    <>
    <div className='flex flex-col font-sf select-none text-text'>
        <div className='flex mb-2 gap-2'>
            <div className='flex-none bg-white drop-shadow rounded-md  w-1/3 '>
                <div className=' bg-trc px-10 py-2 text-center text-white  rounded-t-md'>
                    MY ACCOUNT
                </div>
                <div className=''>
                    <div className=' mt-2 text-center'>
                        <span class="icon-[pajamas--profile] h-12 w-12"></span>
                    </div>
                    <div className='text-center bg-trc text-white text-sm'>ID: 00001</div>
                </div>
                <div className=' px-3 pt-2 pb-2 '>
                    <div className='font-sf'>Last, First Name</div>
                    <div className='font-sf'>Other Info</div>
                    <div className='font-sf'>Other Info</div>
                    <div className='font-sf'>Other Info</div>
                    <div className='font-sf'>Other Info</div>
                </div>
            </div>
            
            <div className='flex-1 flex flex-col '>
                <div className=' hover:scale-101 cursor-pointer flex-1 flex bg-gradient-to-br from-trc to-white mb-2 content-center rounded-md p-4 drop-shadow text-white'>
                    <div className='flex-1 content-center font-sf-extrabold text-4xl'>
                        My Sukikart Partners
                    </div >
                    <div className='flex-none content-center'>
                        <img src={suki} alt='suki' className='w-auto h-20'/>
                    </div>
                </div>
                <div className='flex-1 hover:scale-101 cursor-pointer  flex bg-gradient-to-tr from-trc to-white content-center rounded-md p-4 drop-shadow text-white'>
                    <div className='flex-1 content-center font-sf-extrabold text-4xl'>
                        Transaction History
                    </div >
                    <div className='flex-none content-center'>
                        <img src={th} alt='suki' className='w-auto h-20'/>
                    </div>
                </div>
            </div>
            <div className=' hover:scale-101 cursor-pointer gap-10 flex-none flex bg-gradient-to-l from-trc to-dirty content-center rounded-md p-4 drop-shadow text-white'>
                    <div className='flex-1 content-center font-sf-extrabold text-4xl'>
                        Redeem
                        <div>Points</div>
                    </div >
                    <div className='flex-none content-center'>
                        <img src={coin} alt='suki' className='w-auto h-20'/>
                    </div>
                </div>
        </div>
        <div className='flex-1 flex gap-2 text-white'>
            <div className='flex-1 bg-white rounded-md  drop-shadow'>
                <div className='bg-trc p-3 rounded-t-md'>
                    Total Daily Points
                </div>
                <div className='text-text text-opacity-50 flex text-center py-4 text-5xl'>
                    <div className='flex-1'/>
                    10,000 <div className='text-base ml-2'>pts</div>
                    <div className='flex-1'/>
                </div>
            </div>
            <div className='flex-1 bg-white rounded-md  drop-shadow'>
                <div className='bg-trc p-3 rounded-t-md'>
                    Total Weekly Points
                </div>
                <div className='text-text text-opacity-50 flex text-center py-4 text-5xl'>
                    <div className='flex-1'/>
                    70,000 <div className='text-base ml-2'>pts</div>
                    <div className='flex-1'/>
                </div>
            </div>
        </div>
        
        <div className='mt-5'>
            <div className='mb-2 border-b-2 max-w-96 font-sf-bold text-2xl text-trc border-trc'>
                My Stores
            </div>
            <Store />
            <Store />
            <Store />
            <Store />
            <Store />
            <Store />
        </div>
    </div>
    </>
  )
}

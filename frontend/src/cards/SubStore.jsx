import React from 'react'
import suki from '../assets/suki.png'


export default function SubStore({data}) {
    // console.log(data)
    return (
        <>
        <div className={`flex flex-wrap font-sf gap-2 mb-2 p-4 drop-shadow rounded-md ${data?.store_info?.status === 0 ? 'bg-red-800 opacity-50' : data?.store_info?.status === 1 ? 'bg-white' : 'GRADUATED'}`}>
            <div className="flex-none text-center mr-2 w-full md:w-auto">
            <img src={suki} alt="suki" className="w-auto h-24 mx-auto" /> {/* Centers the image horizontally */}
            <div className="bg-trc rounded text-white px-2">
                STORE NO: {data?.store_no}
            </div>
            </div>
            <div className='flex-1 flex flex-wrap text-center w-full md:w-auto'>
                <div className='flex-1 border-r-2 text-white w-full md:w-1/3' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                    INVITATION CODE
                    </div>
                <div className='text-text text-opacity-50 flex text-center py-4 text-5xl'>
                    <div className='flex-1'/>
                    {data?.invite_code?.code}
                    <div className='flex-1'/>
                    </div>
                </div>
                <div className='flex-1 border-r-2 text-white w-full md:w-1/3' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                    CURRENT POINTS
                    </div>
                    <div className='text-text text-opacity-50 flex text-center py-4 text-5xl'>
                    <div className='flex-1'/>
                    {data?.store_info?.points} / 5000
                    <div className='text-base ml-2'>pts</div>
                    <div className='flex-1'/>
                    </div>
                </div>
                <div className='flex-1 text-white w-full md:w-1/3' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                    STATUS
                    </div>
                    <div className='text-text text-opacity-50 flex text-center py-4 text-4xl'>
                    <div className='flex-1'/>
                    {data?.store_info?.status === 0 ? 'CLOSE' : data?.store_info?.status === 1 ? 'OPEN' : 'HAPPY NEW YEAR'} 

                    <div className='flex-1'/>
                    </div>
                </div>
            </div>
        </div>
        </>
    )
}

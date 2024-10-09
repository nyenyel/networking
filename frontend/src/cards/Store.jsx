import React from 'react'
import suki from '../assets/suki.png'


export default function Store({data}) {
    return (
        <>
        <div className='flex flex-wrap font-sf gap-2 bg-white mb-2 p-4 drop-shadow rounded-md'>
            <div className="flex-none text-center mr-2 w-full md:w-auto">
            <img src={suki} alt="suki" className="w-auto h-24 mx-auto" /> {/* Centers the image horizontally */}
            <div className="bg-trc rounded text-white px-2">
                STORE ID: {data?.user.store_info.id}
            </div>
            </div>
            <div className='flex-1 flex flex-wrap text-center w-full md:w-auto'>
                <div className='flex-1 border-r-2 text-white w-full md:w-1/3' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                    AVG. DAILY POINTS
                    </div>
                    <div className='text-text text-opacity-50 flex text-center py-4 text-5xl'>
                    <div className='flex-1'/>
                    {data?.points.daily} 
                    <div className='text-base ml-2'>pts</div>
                    <div className='flex-1'/>
                    </div>
                </div>
                <div className='flex-1 border-r-2 text-white w-full md:w-1/3' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                    CURRENT POINTS
                    </div>
                    <div className='text-text text-opacity-50 flex text-center py-4 text-5xl'>
                    <div className='flex-1'/>
                    {data?.user.store_info.points} 
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
                    {data?.points.status}
                    <div className='flex-1'/>
                    </div>
                </div>
            </div>
        </div>
        </>
    )
}

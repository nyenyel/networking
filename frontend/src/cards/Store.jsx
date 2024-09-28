import React from 'react'
import suki from '../assets/suki.png'


export default function Store() {
    return (
        <>
        <div className='flex font-sf bg-white mb-2 p-4 drop-shadow rounded-md'>
            <div className="flex-none text-center mr-2">
                <img src={suki} alt="suki" className="w-auto h-24 mx-auto" /> {/* Centers the image horizontally */}
                <div className="bg-trc rounded text-white px-2">STORE ID: 0001</div>
            </div>
            <div className='flex-1 flex text-center'>
                <div className='flex-1 border-r-2 text-white' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                        DAILY POINTS
                    </div>
                    <div className='text-text text-opacity-50 flex text-center py-4 text-5xl'>
                        <div className='flex-1'/>
                        10,000 <div className='text-base ml-2'>pts</div>
                        <div className='flex-1'/>
                    </div>
                </div>
                <div className='flex-1 border-r-2 text-white' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                        TOTAL POINTS
                    </div>
                    <div className='text-text text-opacity-50 flex text-center py-4 text-5xl'>
                        <div className='flex-1'/>
                        70,000 <div className='text-base ml-2'>pts</div>
                        <div className='flex-1'/>
                    </div>
                </div>
                <div className='flex-1  text-white' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                        STATUS
                    </div>
                    <div className='text-text text-opacity-50 flex text-center py-4 text-4xl'>
                        <div className='flex-1'/>
                        OPEN STORE 
                        <div className='flex-1'/>
                    </div>
                </div>

            </div>
        </div>
        </>
    )
}

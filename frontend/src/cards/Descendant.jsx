import React from 'react'
import suki from '../assets/suki.png'


export default function Descendant({data}) {
    return (
        <>
        <div className='flex font-sf bg-white mb-2 p-4 drop-shadow rounded-md'>
            <div className="flex-none text-center mr-2">
                <img src={suki} alt="suki" className="w-auto h-24 mx-auto" /> {/* Centers the image horizontally */}
                <div className="bg-trc rounded text-white px-2">STORE ID: {data?.store_info.id}</div>
            </div>
            <div className='flex-1  flex text-center'>
                <div className='flex-1 text-white mb-8' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                        OWNER
                    </div>
                    <div className='text-text text-opacity-50 flex items-center justify-center h-full  text-2xl'>
                        {data?.last_name}, {data?.first_name} { data?.middle_name.charAt(0)}.
                    </div>
                </div>
                <div className='h-full w-0.5 bg-black bg-opacity-20'/>
                {/* <div className='flex-1 border-r-2 text-white mb-8' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                        NO. OF INVITED USER 
                    </div>
                    <div className='text-text text-opacity-50 flex items-center justify-center h-full  text-2xl'>
                        10 <div className='text-base ml-2'>users</div>
                    </div>
                </div> */}
                <div className='flex-1  text-white mb-8' >
                    <div className='bg-trc mx-4 rounded-md py-1'>
                        STORE STATUS
                    </div>
                    <div className='text-text text-opacity-50 flex items-center justify-center h-full  text-2xl'>
                        {data.status}
                    </div>
                </div>

            </div>
        </div>
        </>
    )
}

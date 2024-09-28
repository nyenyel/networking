import React from 'react'

export default function Call({data}) {
    return (
    <>
    <div className={` hover:scale-105 cursor-pointer flex-1 max-w-96 rounded-md min-w-64 text-black content-center drop-shadow`} style={{ backgroundColor: data.color }}>
        <div className='flex p-5'>
            <div className='flex-1 font-pf content-center'>
                <div>
                    {data.text}
                </div>
                <div className='font-sf-bold flex items-center mt-1 '>
                    Shop Now
                    <span className="icon-[material-symbols--arrow-circle-right] ml-1 h-6 w-6 bg-src"></span>
                </div>
            </div>
            <div className='flex-none content-center'>
                <img src={data.img} alt="image" className="h-24 w-auto" />
            </div>
        </div>
    </div>
    </>
  )
}

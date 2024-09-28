import React from 'react'
import ReactFlow, { MiniMap, Controls, Background, useNodesState, useEdgesState, addEdge } from 'reactflow';
import 'reactflow/dist/style.css';

export default function GenealogyOutlet() {
    const initialNodes = [
        {
          id: '1',
          position: { x: 300, y: 50 },
          data: { label: 'Top Node' },
          style: { backgroundColor: '#FF5722', color: 'white', border: '2px solid #FF9800' }, // Custom color
        },
        {
          id: '2',
          position: { x: 150, y: 150 },
          data: { label: 'Level 2 - Node 1' },
          style: { backgroundColor: '#03A9F4', color: 'white' }, // Custom color
        },
        {
          id: '3',
          position: { x: 450, y: 150 },
          data: { label: 'Level 2 - Node 2' },
          style: { backgroundColor: '#8BC34A', color: 'black' }, // Custom color
        },
        {
          id: '4',
          position: { x: 50, y: 250 },
          data: { label: 'Level 3 - Node 1' },
          style: { backgroundColor: '#FFC107', color: 'black' }, // Custom color
        },
        {
          id: '5',
          position: { x: 300, y: 250 },
          data: { label: 'Level 3 - Node 2' },
          style: { backgroundColor: '#9C27B0', color: 'white' }, // Custom color
        },
        {
          id: '6',
          position: { x: 550, y: 250 },
          data: { label: 'Level 3 - Node 3' },
          style: { backgroundColor: '#607D8B', color: 'white' }, // Custom color
        },
    ];
      
      const initialEdges = [
        { id: 'e1-2', source: '1', target: '2', type: 'bezier' },
        { id: 'e1-3', source: '1', target: '3', type: 'bezier' },
        { id: 'e2-4', source: '2', target: '4', type: 'bezier' },
        { id: 'e2-5', source: '2', target: '5', type: 'bezier' },
        { id: 'e3-6', source: '3', target: '6', type: 'bezier' }
      ];

    const [nodes, setNodes, onNodesChange] = useNodesState(initialNodes);
    const [edges, setEdges, onEdgesChange] = useEdgesState(initialEdges);

    const onConnect = (params) => setEdges((eds) => addEdge(params, eds));
    return (
        <>
        <div className='flex w-full p-4 rounded-md h-screen bg-trc bg-opacity-20'>
            <ReactFlow
                nodes={nodes}
                edges={edges}
                onNodesChange={onNodesChange}
                onEdgesChange={onEdgesChange}
                nodesConnectable={false}  // Disable node connection
                nodesDraggable={false}     // Allow node dragging (optional)
                fitView
            >
                <MiniMap />
                <Controls />
                <Background />
            </ReactFlow>
        </div>
        </>
    )
}

import React from 'react'
import ReactFlow, { MiniMap, Controls, Background, useNodesState, useEdgesState, addEdge } from 'reactflow';
import 'reactflow/dist/style.css';

export default function GenealogyOutlet() {
    const initialNodes = [
        { id: '1', position: { x: 300, y: 50 }, data: { label: 'Top Node' } }, // Top node
        { id: '2', position: { x: 150, y: 150 }, data: { label: 'Level 2 - Node 1' } }, // Level 2
        { id: '3', position: { x: 450, y: 150 }, data: { label: 'Level 2 - Node 2' } },
        { id: '4', position: { x: 50, y: 250 }, data: { label: 'Level 3 - Node 1' } }, // Level 3
        { id: '5', position: { x: 300, y: 250 }, data: { label: 'Level 3 - Node 2' } },
        { id: '6', position: { x: 550, y: 250 }, data: { label: 'Level 3 - Node 3' } }
      ];
      
      const initialEdges = [
        { id: 'e1-2', source: '1', target: '2', type: 'smoothstep' },
        { id: 'e1-3', source: '1', target: '3', type: 'smoothstep' },
        { id: 'e2-4', source: '2', target: '4', type: 'smoothstep' },
        { id: 'e2-5', source: '2', target: '5', type: 'smoothstep' },
        { id: 'e3-6', source: '3', target: '6', type: 'smoothstep' }
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

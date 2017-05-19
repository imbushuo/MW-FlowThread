// Copyright 2017 The Little Moe New LLC. All rights reserved.
// Code licensed under MIT License. For more information, check out LICENSE file in project directory.

/**
 * Class that maps comment reply for mobile devices.
 * @author The Little Moe New LLC
 */
class MobileCommentParentMapping {

    private m_dRelationStorage: { [parentId: string]: string };
    private m_bEmitTraceWarningOnCollision: boolean;

    /**
     * Initializes new instance of the @see MobileCommentParentMapping class.
     */
    constructor(emitTraceWarningOnCollision: boolean = false) {
        this.m_dRelationStorage = {};
        this.m_bEmitTraceWarningOnCollision = emitTraceWarningOnCollision;
    }

    /**
     * Push a mapping relationship to storage.
     * @param parentId Parent comment ID.
     * @param destId Destination comment ID.
     */
    pushMapping(parentId: string, destId: string): void {

        if (this.m_dRelationStorage[parentId] && this.m_bEmitTraceWarningOnCollision) {
            console.warn(`TRACE: Key collision detected when attempt to add relationship ${parentId} to ${destId}.`, {
                originalValue: this.m_dRelationStorage[parentId]
            }); 
        }
        this.m_dRelationStorage[parentId] = destId;

    }

    /**
     * Get top-level comment ID with given ID.
     * @param parentId The ID to query with.
     */
    getMapping(parentId: string): string | null {

        if (!parentId) return null;

        let szDest = this.m_dRelationStorage[parentId];
        let szNgDest: string = null;
        if (szDest) szNgDest = this.getMapping(szDest);
        return (szNgDest) ? szNgDest : szDest;

    }

}
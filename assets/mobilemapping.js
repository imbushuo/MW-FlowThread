// Copyright 2017 The Little Moe New LLC. All rights reserved.
// Code licensed under MIT License. For more information, check out LICENSE file in project directory.
/**
 * Class that maps comment reply for mobile devices.
 * @author The Little Moe New LLC
 */
var MobileCommentParentMapping = (function () {
    /**
     * Initializes new instance of the @see MobileCommentParentMapping class.
     */
    function MobileCommentParentMapping(emitTraceWarningOnCollision) {
        if (emitTraceWarningOnCollision === void 0) { emitTraceWarningOnCollision = false; }
        this.m_dRelationStorage = {};
        this.m_bEmitTraceWarningOnCollision = emitTraceWarningOnCollision;
    }
    /**
     * Push a mapping relationship to storage.
     * @param parentId Parent comment ID.
     * @param destId Destination comment ID.
     */
    MobileCommentParentMapping.prototype.pushMapping = function (parentId, destId) {
        if (this.m_dRelationStorage[parentId] && this.m_bEmitTraceWarningOnCollision) {
            console.warn("TRACE: Key collision detected when attempt to add relationship " + parentId + " to " + destId + ".", {
                originalValue: this.m_dRelationStorage[parentId]
            });
        }
        this.m_dRelationStorage[parentId] = destId;
    };
    /**
     * Get top-level comment ID with given ID.
     * @param parentId The ID to query with.
     */
    MobileCommentParentMapping.prototype.getMapping = function (parentId) {
        if (!parentId)
            return null;
        var szDest = this.m_dRelationStorage[parentId];
        var szNgDest = null;
        if (szDest)
            szNgDest = this.getMapping(szDest);
        return (szNgDest) ? szNgDest : szDest;
    };
    return MobileCommentParentMapping;
}());

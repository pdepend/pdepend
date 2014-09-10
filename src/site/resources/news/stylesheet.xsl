<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
        xmlns:xalan="http://xml.apache.org/xalan">

    <xsl:output
            method="xml"
            indent="yes"
            encoding="UTF-8"
            xalan:indent-amount="4" />
    
    <xsl:param name="project.version" select="''" />


    <xsl:template match="@* | node()">
        <xsl:if test="local-name() = 'entry' and position() = 2">
            <xsl:element name="entry">
                <xsl:element name="title">
                    <xsl:text>PDepend </xsl:text>
                    <xsl:value-of select="$project.version" />
                    <xsl:text> released</xsl:text>
                </xsl:element>
                <xsl:element name="path">
                    <xsl:text>pdepend-</xsl:text>
                    <xsl:value-of select="$project.version" />
                    <xsl:text>-released.rst</xsl:text>
                </xsl:element>
                <xsl:element name="categories">
                    <xsl:element name="category">
                        <xsl:text>announcement</xsl:text>
                    </xsl:element>
                    <xsl:element name="category">
                        <xsl:text>releases</xsl:text>
                    </xsl:element>
                </xsl:element>
            </xsl:element>
        </xsl:if>

        <xsl:copy>
            <xsl:apply-templates select="@* | node()"/>
        </xsl:copy>
    </xsl:template>

</xsl:stylesheet>

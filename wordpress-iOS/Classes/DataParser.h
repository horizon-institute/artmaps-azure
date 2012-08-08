//
//  DataParser.h
//  WordPress
//
//  Created by Shakir Ali on 21/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "ObjectOfInterest.h"

@interface DataParser : NSObject
+(ObjectOfInterest*)getOoIFromDictionary:(NSDictionary*)dict;
+(OoIMeta*)getOoIMetaFromDictionary:(NSDictionary*)dict;
@end
